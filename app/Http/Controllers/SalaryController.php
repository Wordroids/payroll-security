<?php

namespace App\Http\Controllers;

use App\Models\Salary;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Meals;
use App\Models\Site;
use App\Models\Uniform;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use App\Models\SalarySetting;
use Illuminate\Support\Facades\Log;

class SalaryController extends Controller
{
   public function index()
    {
        $employees = Employee::all();
        return view('pages.salaries.index', compact('employees'));
    }

    public function show(Employee $employee, Request $request)
    {
        $month = $request->input('month', now()->format('Y-m'));
        $data = $this->getSalaryData($employee, $month);
        return view('pages.salaries.show-single-salary', $data);
    }

    public function overview(Request $request)
    {
        $month = $request->input('month', now()->format('Y-m'));
        $employeeId = $request->input('employee_id');

        $startDate = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $endDate = Carbon::createFromFormat('Y-m', $month)->endOfMonth();

        $query = Employee::query();
        if ($employeeId) {
            $query->where('id', $employeeId);
        } else {
            $query->whereHas('attendances', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('date', [$startDate, $endDate]);
            });
        }

        $employees = $query->get();
        $allEmployees = Employee::all();
        $salaryData = [];

        foreach ($employees as $employee) {
            $salaryData[] = $this->getSalaryData($employee, $month);
        }

        return view('pages.salaries.overview', [
            'salaryData' => $salaryData,
            'allEmployees' => $allEmployees,
            'selectedEmployee' => $employeeId,
            'month' => $month,
        ]);
    }

    public function exportSalaryOverviewPdf(Request $request)
    {
        $month = $request->input('month', now()->format('Y-m'));
        $employeeId = $request->input('employee_id');

        $startDate = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $endDate = Carbon::createFromFormat('Y-m', $month)->endOfMonth();

        $query = Employee::query();
        if ($employeeId) {
            $query->where('id', $employeeId);
        } else {
            $query->whereHas('attendances', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('date', [$startDate, $endDate]);
            });
        }

        $employees = $query->get();
        $salaryData = [];
        $totalNetPay = 0;

        foreach ($employees as $employee) {
            $data = $this->getSalaryData($employee, $month);
            $salaryData[] = $data;
            $totalNetPay += $data['totalEarnings'];
        }

        $pdfData = [
            'salaryData' => $salaryData,
            'month' => $month,
            'selectedEmployee' => $employeeId,
            'totalNetPay' => $totalNetPay,
        ];

        return Pdf::loadView('pages.salaries.overview-pdf', $pdfData)
            ->setPaper('a4', 'landscape')
            ->download('Salary_Overview_'.$month.'.pdf');
    }

    public function downloadAllSlips(Request $request)
    {
        $month = $request->input('month', now()->format('Y-m'));
        $startDate = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $endDate = Carbon::createFromFormat('Y-m', $month)->endOfMonth();

        $employees = Employee::whereHas('attendances', function ($q) use ($startDate, $endDate) {
            $q->whereBetween('date', [$startDate, $endDate]);
        })->get();

        $allData = [];
        foreach ($employees as $employee) {
            $allData[] = $this->getSalaryData($employee, $month);
        }

        return Pdf::loadView('pages.salaries.all-slips-pdf', [
            'slipsData' => $allData,
            'month' => $month
        ])->setPaper('a4', 'portrait')->download('All_Salary_Slips_'.$month.'.pdf');
    }

    public function viewSlip(Employee $employee, $month)
    {
        $data = $this->getSalaryData($employee, $month);
        return view('pages.salaries.slip-view', $data);
    }

    public function downloadSlip(Employee $employee, $month, Request $request)
    {
        $data = $this->getSalaryData($employee, $month);
        $data['signature'] = $request->input('signature', '');
        $data['date'] = $request->input('date', '');

        return Pdf::loadView('pages.salaries.slip-download', $data)
            ->download(Str::slug($employee->name).'_slip_'.$month.'.pdf');
    }

    private function getSalaryData(Employee $employee, $month)
    {
        try {
            $saved = Salary::where('employee_id', $employee->id)->where('month_year', $month)->first();
            $settings = SalarySetting::getSettings($month);

            $activeBasic = (float)$settings->default_basic_salary;
            $activeBonus = (float)$settings->default_attendance_bonus;
            $specialOtRateVal = (float)$settings->special_ot_rate;

            $startDate = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
            $endDate = Carbon::createFromFormat('Y-m', $month)->endOfMonth();
            $date = Carbon::parse($month);

            $records = Attendance::whereBetween('date', [$startDate, $endDate])->where('employee_id', $employee->id)->get();

            $attendances = [];
            foreach ($records as $attendance) {
                $day = Carbon::parse($attendance->date)->day;
                $siteId = $attendance->site_id;
                $shift = $attendance->shift;
                $hrs = $attendance->worked_hours;

                $siteRecord = Site::find($siteId);
                $attendances[$employee->id][$siteId][$day][$shift] = $hrs;

                if ($shift == 'day') {
                    $attendances[$employee->id][$siteId][$day]['normal_day_hours'] = min($hrs, 9);
                    $attendances[$employee->id][$siteId][$day]['ot_day_hours'] = $siteRecord->has_special_ot_hours ? min(max($hrs - 9, 0), 3) : max($hrs - 9, 0);
                } else {
                    $attendances[$employee->id][$siteId][$day]['normal_night_hours'] = min($hrs, 9);
                    $attendances[$employee->id][$siteId][$day]['ot_night_hours'] = $siteRecord->has_special_ot_hours ? min(max($hrs - 9, 0), 3) : max($hrs - 9, 0);
                }
            }

            $data = [
                'employee' => $employee, 'month' => $month, 'startDate' => $startDate,
                'basic' => $activeBasic, 'attendance_bonus' => $activeBonus,
                'totalShiftEarning' => 0, 'specialOtEarnings' => 0, 'paidOtHours' => 0, 'performanceOtHours' => 0,
                'performanceAllowance' => 0, 'otherAllowances' => 0, 'subTotal' => 0, 'grossPay' => 0, 'otEarnings' => 0,
                'siteSummaries' => [], 'specialOtData' => [], 'total_shifts' => 0, 'attendances' => $attendances,
                'specialOtRate' => $specialOtRateVal, 'specialOtHours' => 0,
                'mealDeductions' => 0, 'uniformDeductions' => 0, 'totalSalaryAdvance' => 0, 'epfDeductEmployee' => 0,
                'totalDeductions' => 0, 'totalEarnings' => 0, 'epfEmployee' => 0, 'etfEmployee' => 0, 'epfEtfEmployer' => 0
            ];

            foreach ($attendances[$employee->id] ?? [] as $siteId => $days) {
                $resolved = $this->resolveRankAndRate($employee, $siteId);
                $siteHrs = 0; $siteSpecDay = 0; $siteSpecNight = 0;
                foreach ($days as $day => $shifts) {
                    if (isset($shifts['day'])) {
                        $siteHrs += $shifts['day'];
                        if ($resolved['site']->has_special_ot_hours) $siteSpecDay += max($shifts['day'] - 12, 0);
                    }
                    if (isset($shifts['night'])) {
                        $siteHrs += $shifts['night'];
                        if ($resolved['site']->has_special_ot_hours) $siteSpecNight += max($shifts['night'] - 12, 0);
                    }
                }
                $sShifts = $siteHrs / 12;
                $data['total_shifts'] += $sShifts;
                $data['totalShiftEarning'] += ($sShifts * $resolved['rate']);
                $sSpecEarnings = ($siteSpecDay + $siteSpecNight) * $specialOtRateVal;
                $data['specialOtEarnings'] += $sSpecEarnings;
                $data['specialOtHours'] += ($siteSpecDay + $siteSpecNight);

                $data['siteSummaries'][] = [
                    'site' => $resolved['site'], 'shifts' => $sShifts, 'earning' => $sShifts * $resolved['rate'],
                    'rank' => $resolved['rank'], 'rate' => $resolved['rate']
                ];
            }

            $otDays = 0;
            foreach ($attendances[$employee->id] ?? [] as $siteId => $days) {
                foreach ($days as $day => $shifts) {
                    $dayOt = ($shifts['ot_day_hours'] ?? 0) + ($shifts['ot_night_hours'] ?? 0);
                    if ($dayOt > 0) {
                        $otDays++;
                        if ($otDays <= 25) $data['paidOtHours'] += $dayOt;
                        else $data['performanceOtHours'] += $dayOt;
                    }
                }
            }

            $otRate = round(($activeBasic / 240 * 1.5), 2);
            $data['otRate'] = $otRate;
            $data['otEarnings'] = round($otRate * $data['paidOtHours'], 2);
            $data['performanceAllowance'] = round($otRate * $data['performanceOtHours'], 2);
            $data['subTotal'] = $data['basic'] + $data['attendance_bonus'] + $data['otEarnings'] + $data['performanceAllowance'];
            $data['grossPay'] = $data['specialOtEarnings'] + $data['totalShiftEarning'];
            $data['otherAllowances'] = max(round($data['totalShiftEarning'] - $data['subTotal'], 2), 0);

            $data['mealDeductions'] = Meals::where('employee_id', $employee->id)->whereMonth('date', $date->month)->whereYear('date', $date->year)->sum('amount');
            $data['totalSalaryAdvance'] = $employee->salaryAdvances()->whereBetween('advance_date', [$startDate, $endDate])->sum('amount');

            $data['epfDeductEmployee'] = $employee->include_epf_etf ? round($activeBasic * 0.08, 2) : 0;
            $data['epfEmployee'] = $employee->include_epf_etf ? round($activeBasic * 0.12, 2) : 0;
            $data['etfEmployee'] = $employee->include_epf_etf ? round($activeBasic * 0.03, 2) : 0;
            $data['epfEtfEmployer'] = $employee->include_epf_etf ? round($activeBasic * 0.15, 2) : 0;

            $currentUniform = Uniform::where('employee_id', $employee->id)->whereMonth('date', $date->month)->whereYear('date', $date->year)->sum('total_amount');
            $data['uniformDeductions'] = $currentUniform / 2;

            $data['totalDeductions'] = $data['epfDeductEmployee'] + $data['totalSalaryAdvance'] + $data['mealDeductions'] + $data['uniformDeductions'];
            $data['totalEarnings'] = $data['grossPay'] - $data['totalDeductions'];

            Salary::updateOrCreate(
                ['employee_id' => $employee->id, 'month_year' => $month],
                ['basic_salary' => $activeBasic, 'attendance_allowance' => $activeBonus, 'ot_earnings' => $data['otEarnings'], 'net_salary' => $data['totalEarnings']]
            );

            if ($saved) {
                $data['basic'] = $saved->basic_salary;
                $data['attendance_bonus'] = $saved->attendance_allowance;
                $data['otEarnings'] = $saved->ot_earnings;
                $data['totalEarnings'] = $saved->net_salary;
            }

            return $data;

        } catch (\Exception $e) {
            Log::error('Error in getSalaryData: ' . $e->getMessage());
            throw $e;
        }
    }

    private function resolveRankAndRate($employee, $siteId)
    {
        $site = $employee->sites->firstWhere('id', $siteId) ?? Site::with('rankRates')->find($siteId);
        $rank = $site->pivot?->rank ?? $employee->rank ?? 'CSO';
        $rate = $site->rankRates->firstWhere('rank', $rank)?->guard_shift_rate ?? $site->guard_shift_rate ?? 0;
        return ['site' => $site, 'rank' => $rank, 'rate' => $rate];
    }
     // to download pdf of salary advances
    public function exportSalaryAdvancesPdf(Request $request)
    {
        // Get filter parameters
        $date = $request->input('date');
        $month = $request->input('month');
        $showAll = $request->boolean('show_all');

        \Log::debug('Export PDF Parameters', [
            'date' => $date,
            'month' => $month,
            'show_all' => $showAll
        ]);

        $query = Employee::query();

        //filters
        if (!$showAll) {
            if ($date) {
                $month = null;
                $query->whereHas('salaryAdvances', function ($q) use ($date) {
                    $q->whereDate('advance_date', $date);
                })->with(['salaryAdvances' => function ($q) use ($date) {
                    $q->whereDate('advance_date', $date);
                }]);
            } elseif ($month) {
                // Clear date , if month is specified
                $date = null;
                $startDate = Carbon::parse($month)->startOfMonth();
                $endDate = Carbon::parse($month)->endOfMonth();

                $query->whereHas('salaryAdvances', function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('advance_date', [$startDate, $endDate]);
                })->with(['salaryAdvances' => function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('advance_date', [$startDate, $endDate]);
                }]);
            } else {
                // Default to today if no specific filter
                $today = now()->format('Y-m-d');
                $query->whereHas('salaryAdvances', function ($q) use ($today) {
                    $q->whereDate('advance_date', $today);
                })->with(['salaryAdvances' => function ($q) use ($today) {
                    $q->whereDate('advance_date', $today);
                }]);
            }
        } else {
            $query->with('salaryAdvances');
        }

        $employees = $query->get();

        // Calculate total
        $totalSalaryAdvances = $employees->sum(function ($employee) {
            return $employee->salaryAdvances->sum('amount');
        });

        // Generate PDF
        $pdf = PDF::loadView('pages.salaries.advances-pdf', [
            'employees' => $employees,
            'totalSalaryAdvances' => $totalSalaryAdvances,
            'date' => $date,
            'month' => $month,
            'showAll' => $showAll
        ])->setPaper('a4', 'landscape');

        // Generate filename
        $filename = 'salary-advances-';
        if ($showAll) {
            $filename .= 'all-records';
        } elseif ($month) {
            $filename .= Carbon::parse($month)->format('F-Y');
        } elseif ($date) {
            $filename .= Carbon::parse($date)->format('Y-m-d');
        } else {
            $filename .= Carbon::now()->format('Y-m-d');
        }
        $filename .= '.pdf';

        return $pdf->download($filename);
    }
}
