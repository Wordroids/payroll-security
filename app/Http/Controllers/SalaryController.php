<?php

namespace App\Http\Controllers;

use App\Models\Salary;
use App\Http\Requests\StoreSalaryRequest;
use App\Http\Requests\UpdateSalaryRequest;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Meals;
use App\Models\Site;
use App\Models\Uniform;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class SalaryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $employees = Employee::all();
        return view('pages.salaries.index', compact('employees'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSalaryRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee, Request $request)
    {
        $employee->load(['sites', 'salaryAdvances']);
        $month = $request->input('month', now()->format('Y-m'));
        $startDate = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $endDate = Carbon::createFromFormat('Y-m', $month)->endOfMonth();
        $prevMonth = Carbon::createFromFormat('Y-m', $month)->subMonth()->format('Y-m');
        // Get attendance
        $records = Attendance::whereBetween('date', [$startDate, $endDate])
            ->where('employee_id', $employee->id)
            ->get();

        // Meal deductions
        $mealDeductions = Meals::where('employee_id', $employee->id)
            ->whereMonth('date', '=', substr($month, 5, 2))
            ->whereYear('date', '=', substr($month, 0, 4))
            ->sum('amount');

        // Uniform deductions (current month/2 + previous month/2)
        $currentMonthUniform = Uniform::where('employee_id', $employee->id)
            ->whereMonth('date', '=', substr($month, 5, 2))
            ->whereYear('date', '=', substr($month, 0, 4))
            ->sum('total_amount');

        $prevMonthUniform = Uniform::where('employee_id', $employee->id)
            ->whereMonth('date', '=', substr($prevMonth, 5, 2))
            ->whereYear('date', '=', substr($prevMonth, 0, 4))
            ->sum('total_amount');

        $uniformDeductions = ($currentMonthUniform / 2) + ($prevMonthUniform / 2);

        $attendances = [];
        foreach ($records as $attendance) {
            $day = Carbon::parse($attendance->date)->day;
            $attendances[$attendance->employee_id][$attendance->site_id][$day][$attendance->shift] = $attendance->worked_hours;
        }
        $specialOtHours = 0;
        $specialOtDayHours = 0;
        $specialOtNightHours = 0;
        $specialOtEarnings = 0;

        $specialOtData = [];

        // Process normal/OT hours and calculate special OT per site
        foreach ($attendances as $empId => $sites) {
            foreach ($sites as $siteId => $days) {
                $site = $employee->sites->find($siteId);
                $siteSpecialOtDayHours = 0;
                $siteSpecialOtNightHours = 0;
                $siteSpecialOtEarnings = 0;


                // to get the employee's rank for this site
                $rank = $site->pivot->rank ?? 'CSO';
                $rankRate = $site->rankRates()->where('rank', $rank)->first();


                $guardShiftRate = $rankRate ? $rankRate->guard_shift_rate : ($site->guard_shift_rate ?? 0);

                foreach ($days as $day => $shifts) {
                    // Day shift calculations
                    if (isset($shifts['day'])) {
                        $dayHours = $shifts['day'];
                        $attendances[$empId][$siteId][$day]['normal_day_hours'] = min($dayHours, 9);
                        $attendances[$empId][$siteId][$day]['ot_day_hours'] = min(max($dayHours - 9, 0), 3);
                        if ($site->has_special_ot_hours) {
                            $specialOtDay = max($dayHours - 12, 0);
                            $attendances[$empId][$siteId][$day]['special_ot_day_hours'] = $specialOtDay;
                            $specialOtDayHours += $specialOtDay;
                            $siteSpecialOtDayHours += $specialOtDay;

                            // Calculate special OT earnings for this site
                            if ($guardShiftRate > 0) {
                                $specialOtRate = 200;
                                $siteSpecialOtEarnings += $specialOtDay * $specialOtRate;
                                $specialOtEarnings += $specialOtDay * $specialOtRate;
                            }
                        }
                    }

                    // Night shift calculations
                    if (isset($shifts['night'])) {
                        $nightHours = $shifts['night'];
                        $attendances[$empId][$siteId][$day]['normal_night_hours'] = min($nightHours, 9);
                        $attendances[$empId][$siteId][$day]['ot_night_hours'] = min(max($nightHours - 9, 0), 3);
                        if ($site->has_special_ot_hours) {
                            $specialOtNight = max($nightHours - 12, 0);
                            $attendances[$empId][$siteId][$day]['special_ot_night_hours'] = $specialOtNight;
                            $specialOtNightHours += $specialOtNight;
                            $siteSpecialOtNightHours += $specialOtNight;

                            // Calculate special OT earnings for this site
                            if ($guardShiftRate > 0) {
                                $specialOtRate = 200;
                                $siteSpecialOtEarnings += $specialOtNight * $specialOtRate;
                                $specialOtEarnings += $specialOtNight * $specialOtRate;
                        }
                    }
                    }
                }

                // Store special OT data for this site
                if ($site->has_special_ot_hours && ($siteSpecialOtDayHours > 0 || $siteSpecialOtNightHours > 0)) {
                    $specialOtRate = $guardShiftRate > 0 ? 200 : 0;

                    $specialOtData[$siteId] = [
                        'site' => $site,
                        'day_hours' => $siteSpecialOtDayHours,
                        'night_hours' => $siteSpecialOtNightHours,
                        'total_hours' => $siteSpecialOtDayHours + $siteSpecialOtNightHours,
                        'rate' => $specialOtRate,
                        'earnings' => $siteSpecialOtEarnings,
                        'rank' => $rank,
                        'guard_shift_rate' => $guardShiftRate
                    ];
                }
            }
        }

        $specialOtHours = $specialOtDayHours + $specialOtNightHours;

        // Total Salary Advances
        $salaryAdvances = $employee->salaryAdvances()
            ->whereBetween('advance_date', [$startDate, $endDate])
            ->get();
        $employee->totalSalaryAdvance = $salaryAdvances->sum('amount');
        // Initialize earnings
        $totalShiftEarning = 0;
        $totalOTHours = 0;
        // Calculate per-site earnings
        $siteSummaries = [];
        foreach ($employee->sites as $site) {
            $normalDayHours = 0;
            $normalNightHours = 0;
            $otDayHours = 0;
            $otNightHours = 0;
            // Get the employee's rank
            $rank = $site->pivot->rank ?? 'CSO';

            // Get the shift rate for the rank
            $rankRate = $site->rankRates()->where('rank', $rank)->first();
            $shiftRate = $rankRate ? $rankRate->guard_shift_rate : $site->guard_shift_rate;

            if (!$shiftRate) {
                \Log::warning("No shift rate found for site {$site->id} - using fallback");
                $shiftRate = 0;
            }
            for ($d = 1; $d <= $startDate->daysInMonth; $d++) {
                $normalDayHours += $attendances[$employee->id][$site->id][$d]['normal_day_hours'] ?? 0;
                $normalNightHours += $attendances[$employee->id][$site->id][$d]['normal_night_hours'] ?? 0;
                $otDayHours += $attendances[$employee->id][$site->id][$d]['ot_day_hours'] ?? 0;
                $otNightHours += $attendances[$employee->id][$site->id][$d]['ot_night_hours'] ?? 0;
            }
            $totalSiteHours = $normalDayHours + $normalNightHours + $otDayHours + $otNightHours;
            $siteShifts = $totalSiteHours / 12;
            $siteEarning = ($totalSiteHours / 12) * $shiftRate;
            $totalShiftEarning += $siteEarning;
            $totalOTHours += $otDayHours + $otNightHours;
            $siteSummaries[] = [
                'site' => $site,
                'shifts' => $siteShifts,
                'earning' => $siteEarning,
                'rank' => $rank,
                'rate' => $shiftRate
            ];
        }

        // Count OT days and separate OT hours for payment vs performance allowance
        $otDays = 0;
        $paidOtHours = 0;
        $performanceOtHours = 0;

        foreach ($attendances as $empId => $sites) {
            foreach ($sites as $siteId => $days) {
                foreach ($days as $day => $shifts) {
                    $dayOtHours = ($shifts['ot_day_hours'] ?? 0) + ($shifts['ot_night_hours'] ?? 0);

                    if ($dayOtHours > 0) {
                        $otDays++;

                        if ($otDays <= 25) {
                            $paidOtHours += $dayOtHours;
                        } else {
                            $performanceOtHours += $dayOtHours;
                        }
                    }
                }
            }
        }

        // Calculate OT earnings and performance allowance
        $otRate = round(($employee->basic / 240 * 1.5), 2);
        $otEarnings = round($otRate * $paidOtHours, 2);
        $performanceAllowance = round($otRate * $performanceOtHours, 2);

        $subTotal = $employee->basic + $employee->attendance_bonus + $otEarnings + $performanceAllowance;
        $otherAllowances = max(round($totalShiftEarning - $subTotal, 2), 0);

        // gross pay
        $grossPay = $specialOtEarnings + $totalShiftEarning;

        $epfEmployee =  $employee->include_epf_etf ? ($employee->basic / 100) * 12 : 0;
        $etfEmployee =  $employee->include_epf_etf ? ($employee->basic / 100) * 3 : 0;
        $epfDeductEmployee =  $employee->include_epf_etf ? ($employee->basic / 100) * 8 : 0;
        $epfEtfEmployer =  $employee->include_epf_etf ? ($employee->basic / 100) * 15 : 0;
        $totalDeductions = $epfDeductEmployee + $employee->totalSalaryAdvance + $mealDeductions + $uniformDeductions;
        $totalEarnings = $grossPay - $totalDeductions;

        return view('pages.salaries.show-single-salary', compact(
            'employee',
            'month',
            'attendances',
            'siteSummaries',
            'totalShiftEarning',
            'totalOTHours',
            'otRate',
            'otEarnings',
            'otherAllowances',
            'subTotal',
            'grossPay',
            'epfEmployee',
            'epfEtfEmployer',
            'etfEmployee',
            'totalDeductions',
            'totalEarnings',
            'specialOtHours',
            'mealDeductions',
            'uniformDeductions',
            'specialOtDayHours',
            'specialOtNightHours',
            'specialOtEarnings',
            'startDate',
            'otDays',
            'paidOtHours',
            'performanceOtHours',
            'performanceAllowance',
            'specialOtData'
        ));
    }

    /**
     * Display salary overview for all employees
     */
    public function overview(Request $request)
    {
        // Get filter values from request or use defaults
        $month = $request->input('month', now()->format('Y-m'));
        $employeeId = $request->input('employee_id');

        $startDate = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $endDate = Carbon::createFromFormat('Y-m', $month)->endOfMonth();
        $prevMonth = Carbon::createFromFormat('Y-m', $month)->subMonth()->format('Y-m');

        // Base query for employees
        $query = Employee::query();

        // Filter by employee if selected
        if ($employeeId) {
            $query->where('id', $employeeId);
        }

        $employees = $query->get();
        $allEmployees = Employee::all(); // For dropdown

        $salaryData = [];

        foreach ($employees as $employee) {
            $employee->load(['sites', 'salaryAdvances']);

            // Get attendance
            $records = Attendance::whereBetween('date', [$startDate, $endDate])
                ->where('employee_id', $employee->id)
                ->get();

            $attendances = [];
            $specialOtHours = 0;
            $specialOtEarnings = 0;

            foreach ($records as $attendance) {
                $day = Carbon::parse($attendance->date)->day;
                $attendances[$attendance->employee_id][$attendance->site_id][$day][$attendance->shift] = $attendance->worked_hours;
            }

            // Process normal/OT hours with per-site special OT calculation
            foreach ($attendances as $empId => $sites) {
                foreach ($sites as $siteId => $days) {
                    $site = Site::find($siteId);
                // Get the employee's rank for this site
                    $rank = $site->pivot->rank ?? 'CSO';
                    $rankRate = $site->rankRates()->where('rank', $rank)->first();

                    // Use rank-specific rate if available, otherwise fallback to site rate
                    $guardShiftRate = $rankRate ? $rankRate->guard_shift_rate : ($site->guard_shift_rate ?? 0);

                        foreach ($days as $day => $shifts) {
                            if (isset($shifts['day'])) {
                           $dayHours = $shifts['day'];
                            $attendances[$empId][$siteId][$day]['normal_day_hours'] = min($dayHours, 9);
                                $attendances[$empId][$siteId][$day]['ot_day_hours'] = min(max($dayHours - 9, 0), 3);

                            if ($site->has_special_ot_hours) {
                                $specialOtDay = max($dayHours - 12, 0);
                                $specialOtHours += $specialOtDay;
                                if ($guardShiftRate > 0) {
                                    $specialOtRate = 200;
                                    $specialOtEarnings += $specialOtDay * $specialOtRate;
                                }
                            }
                            }
                            if (isset($shifts['night'])) {
                            $nightHours = $shifts['night'];
                             $attendances[$empId][$siteId][$day]['normal_night_hours'] = min($nightHours, 9);
                            $attendances[$empId][$siteId][$day]['ot_night_hours'] = min(max($nightHours - 9, 0), 3);
                            if ($site->has_special_ot_hours) {
                                $specialOtNight = max($nightHours - 12, 0);
                                $specialOtHours += $specialOtNight;
                                if ($guardShiftRate > 0) {
                                    $specialOtRate = 200;
                                    $specialOtEarnings += $specialOtNight * $specialOtRate;
                                }
                            }
                        }
                    }
                }
            }

            // Count OT days and separate OT hours
            $otDays = 0;
            $paidOtHours = 0;
            $performanceOtHours = 0;

            foreach ($attendances as $empId => $sites) {
                foreach ($sites as $siteId => $days) {
                    foreach ($days as $day => $shifts) {
                        $dayOtHours = ($shifts['ot_day_hours'] ?? 0) + ($shifts['ot_night_hours'] ?? 0);

                        if ($dayOtHours > 0) {
                            $otDays++;

                            if ($otDays <= 25) {
                                $paidOtHours += $dayOtHours;
                            } else {
                                $performanceOtHours += $dayOtHours;
                            }
                        }
                    }
                }
            }

            // Total Salary Advances
            $salaryAdvances = $employee->salaryAdvances()
                ->whereBetween('advance_date', [$startDate, $endDate])
                ->get();

            $totalSalaryAdvance = $salaryAdvances->sum('amount');
            // Meal deductions
            $mealDeductions = Meals::where('employee_id', $employee->id)
                ->whereMonth('date', '=', substr($month, 5, 2))
                ->whereYear('date', '=', substr($month, 0, 4))
                ->sum('amount');

            // Uniform deductions (current month/2 + previous month/2)
            $currentMonthUniform = Uniform::where('employee_id', $employee->id)
                ->whereMonth('date', '=', substr($month, 5, 2))
                ->whereYear('date', '=', substr($month, 0, 4))
                ->sum('total_amount');

            $prevMonthUniform = Uniform::where('employee_id', $employee->id)
                ->whereMonth('date', '=', substr($prevMonth, 5, 2))
                ->whereYear('date', '=', substr($prevMonth, 0, 4))
                ->sum('total_amount');
            $uniformDeductions = ($currentMonthUniform / 2) + ($prevMonthUniform / 2);

            // Initialize earnings
            $totalShiftEarning = 0;
            $totalOTHours = 0;
            $totalShifts = 0;

            // Calculate per-site earnings
            foreach ($employee->sites as $site) {
                $normalDayHours = 0;
                $normalNightHours = 0;
                $otDayHours = 0;
                $otNightHours = 0;

                // Get the employee's rank for this site
                $rank = $site->pivot->rank ?? 'CSO';
                $rankRate = $site->rankRates()->where('rank', $rank)->first();
                $shiftRate = $rankRate ? $rankRate->guard_shift_rate : $site->guard_shift_rate;

                if (!$shiftRate) {
                    \Log::warning("No shift rate found for site {$site->id} - using fallback");
                    $shiftRate = 0;
                }

                for ($d = 1; $d <= $startDate->daysInMonth; $d++) {
                    $normalDayHours += $attendances[$employee->id][$site->id][$d]['normal_day_hours'] ?? 0;
                    $normalNightHours += $attendances[$employee->id][$site->id][$d]['normal_night_hours'] ?? 0;
                    $otDayHours += $attendances[$employee->id][$site->id][$d]['ot_day_hours'] ?? 0;
                    $otNightHours += $attendances[$employee->id][$site->id][$d]['ot_night_hours'] ?? 0;
                }

                $totalSiteHours = $normalDayHours + $normalNightHours + $otDayHours + $otNightHours;
                $siteShifts = $totalSiteHours / 12;
                $siteEarning = ($totalSiteHours / 12) * $shiftRate;

                $totalShiftEarning += $siteEarning;
                $totalOTHours += $otDayHours + $otNightHours;
                $totalShifts += $siteShifts;
            }
            // OT rate and performance allowance
            $otRate = round(($employee->basic / 240 * 1.5), 2);
            $otEarnings = round($otRate * $paidOtHours, 2);
            $performanceAllowance = round($otRate * $performanceOtHours, 2);

            $subTotal = $employee->basic + $employee->attendance_bonus + $otEarnings + $performanceAllowance;
            $otherAllowances = max(round($totalShiftEarning - $subTotal, 2), 0);

            // gross pay
            $grossPay = $specialOtEarnings + $totalShiftEarning;
            $epfEmployee =  $employee->include_epf_etf ?($employee->basic / 100) * 12:0;
            $etfEmployee =  $employee->include_epf_etf ?($employee->basic / 100) * 3:0;
            $epfDeductEmployee =  $employee->include_epf_etf ?($employee->basic / 100) * 8:0;
            $totalDeductions = $epfDeductEmployee + $totalSalaryAdvance + $mealDeductions + $uniformDeductions;
            $salaryData[] = [
                'employee' => $employee,
                'total_shifts' => $totalShifts,
                'basic' => $employee->basic,
                'ot_earnings' => $otEarnings,
                'performance_allowance' => $performanceAllowance,
                'special_ot_earnings' => $specialOtEarnings,
                'totalShiftEarning' => $totalShiftEarning,
                'attendance_bonus' => $employee->attendance_bonus,
                'other_allowances' => $otherAllowances,
                'sub_total' => $subTotal,
                'gross_pay' => $grossPay,
                'salary_advance' => $totalSalaryAdvance,
                'meal_deductions' => $mealDeductions,
                'uniform_deductions' => $uniformDeductions,
                 'epf_deduct_employee' => $epfDeductEmployee,
                'total_deductions' => $totalDeductions,
                'net_pay' => $grossPay - $totalDeductions,
                'epf_employee' => $epfEmployee,
                'etf_employee' => $etfEmployee,
                'ot_days' => $otDays,
                'paid_ot_hours' => $paidOtHours,
                'performance_ot_hours' => $performanceOtHours,
            ];
        }

        return view('pages.salaries.overview', [
            'salaryData' => $salaryData,
            'allEmployees' => $allEmployees,
            'selectedEmployee' => $employeeId,
            'month' => $month,
        ]);
    }
    /**
     * Show the salary slip in a preview modal
     */
    public function viewSlip(Employee $employee, $month)
    {
        $data = $this->getSalaryData($employee, $month);
        return view('pages.salaries.slip-view', $data);
    }

    /**
     * Download the salary slip in selected format
     */
    public function downloadSlip(Employee $employee, $month, Request $request)
    {
        $data = $this->getSalaryData($employee, $month);

        // Add signature and date
        $data['signature'] = $request->input('signature', '');
        $data['date'] = $request->input('date', '');

        $filename = Str::slug($employee->name) . '_salary_slip_' . $month;

        switch ($request->input('format', 'pdf')) {
            case 'image':
                $pdf = Pdf::loadView('pages.salaries.slip-download', $data);
                return $pdf->download("{$filename}.png");

            case 'doc':
                $view = view('pages.salaries.slip-download', $data);
                return response($view, 200)
                    ->header('Content-Type', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document')
                    ->header('Content-Disposition', "attachment; filename=\"{$filename}.docx\"");

            case 'pdf':
            default:
                $pdf = Pdf::loadView('pages.salaries.slip-download', $data);
                return $pdf->download("{$filename}.pdf");
        }
    }

    /**
     * Method to get salary data for slip generation
     */
    private function getSalaryData(Employee $employee, $month)
    {
        try {
        $employee->load(['sites' => function ($query) {
                $query->with('rankRates');
            }, 'salaryAdvances']);

        $startDate = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $endDate = Carbon::createFromFormat('Y-m', $month)->endOfMonth();
        $prevMonth = Carbon::createFromFormat('Y-m', $month)->subMonth()->format('Y-m');

            // Initialize all variables with default values
            $data = [
                'employee' => $employee,
                'month' => $month,
                'basic' => $employee->basic ?? 0,
                'attendance_bonus' => $employee->attendance_bonus ?? 0,
                'mealDeductions' => 0,
                'uniformDeductions' => 0,
                'totalSalaryAdvance' => 0,
                'totalShiftEarning' => 0,
                'totalOTHours' => 0,
                'otEarnings' => 0,
                'performanceAllowance' => 0,
                'specialOtHours' => 0,
                'specialOtEarnings' => 0,
                'otherAllowances' => 0,
                'grossPay' => 0,
                'epfEmployee' => 0,
                'etfEmployee' => 0,
                'epfDeductEmployee' => 0,
                'totalDeductions' => 0,
                'totalEarnings' => 0,
                'siteSummaries' => [],
                'otRate' => 0,
                'otDays' => 0,
                'paidOtHours' => 0,
                'performanceOtHours' => 0,
                'specialOtData' => [],
                'siteSpecialOtSummaries' => [],
            ];

        // Meal deductions
        $data['mealDeductions'] = Meals::where('employee_id', $employee->id)
            ->whereMonth('date', '=', substr($month, 5, 2))
            ->whereYear('date', '=', substr($month, 0, 4))
            ->sum('amount');

        // Uniform deductions
        $currentMonthUniform = Uniform::where('employee_id', $employee->id)
            ->whereMonth('date', '=', substr($month, 5, 2))
            ->whereYear('date', '=', substr($month, 0, 4))
            ->sum('total_amount');

        $prevMonthUniform = Uniform::where('employee_id', $employee->id)
            ->whereMonth('date', '=', substr($prevMonth, 5, 2))
            ->whereYear('date', '=', substr($prevMonth, 0, 4))
            ->sum('total_amount');

        $data['uniformDeductions'] = ($currentMonthUniform / 2) + ($prevMonthUniform / 2);

            // Salary Advances
            $salaryAdvances = $employee->salaryAdvances()
                ->whereBetween('advance_date', [$startDate, $endDate])
                ->get();
            $data['totalSalaryAdvance'] = $salaryAdvances->sum('amount');

            // Attendance records
        $records = Attendance::whereBetween('date', [$startDate, $endDate])
            ->where('employee_id', $employee->id)
            ->get();

        $attendances = [];
            $specialOtDayHours = 0;
            $specialOtNightHours = 0;
            $specialOtEarnings = 0;

        foreach ($records as $attendance) {
            $day = Carbon::parse($attendance->date)->day;
            $attendances[$attendance->employee_id][$attendance->site_id][$day][$attendance->shift] = $attendance->worked_hours;
        }

        // Process attendance data and calculate special OT per site
        foreach ($attendances as $empId => $sites) {
            foreach ($sites as $siteId => $days) {
                $site = Site::find($siteId);
                    $siteSpecialOtDayHours = 0;
                    $siteSpecialOtNightHours = 0;
                    $siteSpecialOtEarnings = 0;

                    // Get the employee's rank for this site
                    $rank = $site->pivot->rank ?? 'CSO';
                    $rankRate = $site->rankRates()->where('rank', $rank)->first();

                    // Use rank-specific rate if available
                    $guardShiftRate = $rankRate ? $rankRate->guard_shift_rate : ($site->guard_shift_rate ?? 0);

                    foreach ($days as $day => $shifts) {
                        if (isset($shifts['day'])) {
                            $dayHours = $shifts['day'];
                            $attendances[$empId][$siteId][$day]['normal_day_hours'] = min($dayHours, 9);
                            $attendances[$empId][$siteId][$day]['ot_day_hours'] = min(max($dayHours  - 9, 0), 3);

                            if ($site->has_special_ot_hours) {
                                $specialOtDay = max($dayHours - 12, 0);
                                $attendances[$empId][$siteId][$day]['special_ot_day_hours'] = $specialOtDay;
                            $specialOtDayHours += $specialOtDay;
                                $siteSpecialOtDayHours += $specialOtDay;

                                // Calculate special OT earnings for this site
                                if ($guardShiftRate > 0) {
                                    $specialOtRate = 200;
                                    $siteSpecialOtEarnings += $specialOtDay * $specialOtRate;
                                    $specialOtEarnings += $specialOtDay * $specialOtRate;
                                }
                        }
                        }

                        if (isset($shifts['night'])) {
                            $nightHours = $shifts['night'];
                            $attendances[$empId][$siteId][$day]['normal_night_hours'] = min($nightHours, 9);
                            $attendances[$empId][$siteId][$day]['ot_night_hours'] = min(max($nightHours - 9, 0), 3);

                            if ($site->has_special_ot_hours) {
                                $specialOtNight = max($nightHours - 12, 0);
                                $attendances[$empId][$siteId][$day]['special_ot_night_hours'] = $specialOtNight;
                                $specialOtNightHours += $specialOtNight;
                                $siteSpecialOtNightHours += $specialOtNight;

                                // Calculate special OT earnings for this site
                                if ($guardShiftRate > 0) {
                                    $specialOtRate = 200;
                                    $siteSpecialOtEarnings += $specialOtNight * $specialOtRate;
                                    $specialOtEarnings += $specialOtNight * $specialOtRate;
                            }
                        }
                        }
                    }

                    // Store special OT data for this site
                    if ($site->has_special_ot_hours && ($siteSpecialOtDayHours > 0 || $siteSpecialOtNightHours > 0)) {
                        $specialOtRate = $guardShiftRate > 0 ? 200 : 0;

                        $data['specialOtData'][$siteId] = [
                            'site' => $site,
                            'day_hours' => $siteSpecialOtDayHours,
                            'night_hours' => $siteSpecialOtNightHours,
                            'total_hours' => $siteSpecialOtDayHours + $siteSpecialOtNightHours,
                            'rate' => $specialOtRate,
                            'earnings' => $siteSpecialOtEarnings,
                            'rank' => $rank,
                            'guard_shift_rate' => $guardShiftRate
                        ];


                        $data['siteSpecialOtSummaries'][] = [
                            'site_name' => $site->name,
                            'day_hours' => $siteSpecialOtDayHours,
                            'night_hours' => $siteSpecialOtNightHours,
                            'total_hours' => $siteSpecialOtDayHours + $siteSpecialOtNightHours,
                            'rate' => $specialOtRate,
                            'earnings' => $siteSpecialOtEarnings
                        ];
                    }
                }
            }

            $data['specialOtHours'] = $specialOtDayHours + $specialOtNightHours;
            $data['specialOtEarnings'] = $specialOtEarnings;
            $data['specialOtDayHours'] = $specialOtDayHours;
            $data['specialOtNightHours'] = $specialOtNightHours;
            //  OT days and separate OT hours
            $otDays = 0;
            $paidOtHours = 0;
            $performanceOtHours = 0;

            foreach ($attendances as $empId => $sites) {
                foreach ($sites as $siteId => $days) {
                    foreach ($days as $day => $shifts) {
                        $dayOtHours = ($shifts['ot_day_hours'] ?? 0) + ($shifts['ot_night_hours'] ?? 0);

                        if ($dayOtHours > 0) {
                            $otDays++;

                            if ($otDays <= 25) {
                                $paidOtHours += $dayOtHours;
                            } else {
                                $performanceOtHours += $dayOtHours;
                            }
                        }
                    }
                }
            }

            $data['otDays'] = $otDays;
            $data['paidOtHours'] = $paidOtHours;
            $data['performanceOtHours'] = $performanceOtHours;

            // Calculate per-site earnings
        $siteSummaries = [];
        foreach ($employee->sites as $site) {
            $normalDayHours = 0;
            $normalNightHours = 0;
            $otDayHours = 0;
            $otNightHours = 0;

                // Get the employee's rank for site
                $rank = $site->pivot->rank ?? 'CSO';
                $rankRate = $site->rankRates->firstWhere('rank', $rank);
                $shiftRate = $rankRate ? $rankRate->guard_shift_rate : ($site->guard_shift_rate ?? 0);

            for ($d = 1; $d <= $startDate->daysInMonth; $d++) {
                $normalDayHours += $attendances[$employee->id][$site->id][$d]['normal_day_hours'] ?? 0;
                $normalNightHours += $attendances[$employee->id][$site->id][$d]['normal_night_hours'] ?? 0;
                $otDayHours += $attendances[$employee->id][$site->id][$d]['ot_day_hours'] ?? 0;
                $otNightHours += $attendances[$employee->id][$site->id][$d]['ot_night_hours'] ?? 0;
            }

            $totalSiteHours = $normalDayHours + $normalNightHours + $otDayHours + $otNightHours;
            $siteShifts = $totalSiteHours / 12;
            $siteEarning = $siteShifts * $shiftRate;

            $data['totalShiftEarning'] += $siteEarning;
            $data['totalOTHours'] += $otDayHours + $otNightHours;

            $siteSummaries[] = [
                'site' => $site,
                'shifts' => $siteShifts,
                'earning' => $siteEarning,
                'rank' => $rank,
                'rate' => $shiftRate
            ];
        }

            $data['siteSummaries'] = $siteSummaries;

        // OT rate and performance allowance
        $otRate = round(($employee->basic / 240 * 1.5), 2);
        $data['otRate'] = $otRate;
        $data['otEarnings'] = round($otRate * $paidOtHours, 2);
        $data['performanceAllowance'] = round($otRate * $performanceOtHours, 2);
        $subTotal = $data['basic'] + $data['attendance_bonus'] + $data['otEarnings'] + $data['performanceAllowance'];
        $data['otherAllowances'] = max(round($data['totalShiftEarning'] - $subTotal, 2), 0);
        // gross pay
        $data['grossPay'] = $data['specialOtEarnings'] + $data['totalShiftEarning'];
        $data['epfEmployee'] = $employee->include_epf_etf ? ($employee->basic / 100) * 12 : 0;
        $data['etfEmployee'] = $employee->include_epf_etf ? ($employee->basic / 100) * 3 : 0;
        $data['epfDeductEmployee'] = $employee->include_epf_etf ? ($employee->basic / 100) * 8 : 0;
        $data['totalDeductions'] = $data['epfDeductEmployee'] + $data['totalSalaryAdvance'] + $data['mealDeductions'] + $data['uniformDeductions'];
            $data['totalEarnings'] = $data['grossPay'] - $data['totalDeductions'];

            return $data;
        } catch (\Exception $e) {
            \Log::error('Error in getSalaryData: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            throw $e;
        }
    }

    //to print salary overview

    public function exportSalaryOverviewPdf(Request $request)
    {

        $month = $request->input('month', now()->format('Y-m'));
        $employeeId = $request->input('employee_id');

        $data = [
            'salaryData' => $this->getSalaryOverviewData($month, $employeeId),
            'month' => $month,
            'selectedEmployee' => $employeeId,
        ];


        $filename = 'salary_overview_' . $month;
        if ($employeeId) {
            $employee = Employee::find($employeeId);
            $filename .= '_' . Str::slug($employee->name);
        }
        $filename .= '.pdf';

        // Load the PDF view
        $pdf = PDF::loadView('pages.salaries.overview-pdf', $data)
            ->setPaper('a4', 'landscape')
            ->setOption('margin-top', 10)
            ->setOption('margin-bottom', 10)
            ->setOption('margin-left', 5)
            ->setOption('margin-right', 5);

        return $pdf->download($filename);
    }
    private function getSalaryOverviewData($month, $employeeId)
    {
        $startDate = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $endDate = Carbon::createFromFormat('Y-m', $month)->endOfMonth();
        $prevMonth = Carbon::createFromFormat('Y-m', $month)->subMonth()->format('Y-m');

        $query = Employee::query();

        if ($employeeId) {
            $query->where('id', $employeeId);
        }

        $employees = $query->get();
        $salaryData = [];

        foreach ($employees as $employee) {
            $employee->load(['sites', 'salaryAdvances']);

            // Get attendance
            $records = Attendance::whereBetween('date', [$startDate, $endDate])
                ->where('employee_id', $employee->id)
                ->get();

            $attendances = [];
            $specialOtHours = 0;
            $specialOtEarnings = 0;

            foreach ($records as $attendance) {
                $day = Carbon::parse($attendance->date)->day;
                $attendances[$attendance->employee_id][$attendance->site_id][$day][$attendance->shift] = $attendance->worked_hours;
            }

            // Process normal/OT hours
            foreach ($attendances as $empId => $sites) {
                foreach ($sites as $siteId => $days) {
                    $site = Site::find($siteId);

                    // Get the employee's rank for this site
                    $rank = $site->pivot->rank ?? 'CSO';
                    $rankRate = $site->rankRates()->where('rank', $rank)->first();

                    $guardShiftRate = $rankRate ? $rankRate->guard_shift_rate : ($site->guard_shift_rate ?? 0);

                        foreach ($days as $day => $shifts) {
                            if (isset($shifts['day'])) {
                            $dayHours = $shifts['day'];
                            $attendances[$empId][$siteId][$day]['normal_day_hours'] = min($dayHours, 9);
                            $attendances[$empId][$siteId][$day]['ot_day_hours'] = min(max($dayHours - 9, 0), 3);

                            if ($site->has_special_ot_hours) {
                                $specialOtDay = max($dayHours - 12, 0);
                                $specialOtHours += $specialOtDay;
                                // Calculate earnings for this site's special OT
                                if ($guardShiftRate > 0) {
                                    $specialOtRate = 200;
                                    $specialOtEarnings += $specialOtDay * $specialOtRate;
                                }
                            }
                            }
                            if (isset($shifts['night'])) {
                            $nightHours = $shifts['night'];
                            $attendances[$empId][$siteId][$day]['normal_night_hours'] = min($nightHours, 9);
                            $attendances[$empId][$siteId][$day]['ot_night_hours'] = min(max($nightHours - 9, 0), 3);
                            if ($site->has_special_ot_hours) {
                                $specialOtNight = max($nightHours - 12, 0);
                                $specialOtHours += $specialOtNight;
                                // Calculate earnings for this site's special OT
                                if ($guardShiftRate > 0) {
                                    $specialOtRate = 200;
                                    $specialOtEarnings += $specialOtNight * $specialOtRate;
                            }
                        }
                    }
                }
            }
            }
            //  OT days and separate OT hours
            $otDays = 0;
            $paidOtHours = 0;
            $performanceOtHours = 0;

            foreach ($attendances as $empId => $sites) {
                foreach ($sites as $siteId => $days) {
                    foreach ($days as $day => $shifts) {
                        $dayOtHours = ($shifts['ot_day_hours'] ?? 0) + ($shifts['ot_night_hours'] ?? 0);

                        if ($dayOtHours > 0) {
                            $otDays++;

                            if ($otDays <= 25) {
                                $paidOtHours += $dayOtHours;
                            } else {
                                $performanceOtHours += $dayOtHours;
                            }
                        }
                    }
                }
            }

            // Total Salary Advances
            $salaryAdvances = $employee->salaryAdvances()
                ->whereBetween('advance_date', [$startDate, $endDate])
                ->get();

            $totalSalaryAdvance = $salaryAdvances->sum('amount');

            // Meal deductions
            $mealDeductions = Meals::where('employee_id', $employee->id)
                ->whereMonth('date', '=', substr($month, 5, 2))
                ->whereYear('date', '=', substr($month, 0, 4))
                ->sum('amount');

            // Uniform deductions (current month/2 + previous month/2)
            $currentMonthUniform = Uniform::where('employee_id', $employee->id)
                ->whereMonth('date', '=', substr($month, 5, 2))
                ->whereYear('date', '=', substr($month, 0, 4))
                ->sum('total_amount');

            $prevMonthUniform = Uniform::where('employee_id', $employee->id)
                ->whereMonth('date', '=', substr($prevMonth, 5, 2))
                ->whereYear('date', '=', substr($prevMonth, 0, 4))
                ->sum('total_amount');

            $uniformDeductions = ($currentMonthUniform / 2) + ($prevMonthUniform / 2);

            // Initialize earnings
            $totalShiftEarning = 0;
            $totalOTHours = 0;
            $totalShifts = 0;

            // Calculate per-site earnings
            foreach ($employee->sites as $site) {
                $normalDayHours = 0;
                $normalNightHours = 0;
                $otDayHours = 0;
                $otNightHours = 0;

                // Get the employee's rank for the site
                $rank = $site->pivot->rank ?? 'CSO';
                $rankRate = $site->rankRates()->where('rank', $rank)->first();
                $shiftRate = $rankRate ? $rankRate->guard_shift_rate : $site->guard_shift_rate;

                if (!$shiftRate) {
                    \Log::warning("No shift rate found for site {$site->id} - using fallback");
                    $shiftRate = 0;
                }

                for ($d = 1; $d <= $startDate->daysInMonth; $d++) {
                    $normalDayHours += $attendances[$employee->id][$site->id][$d]['normal_day_hours'] ?? 0;
                    $normalNightHours += $attendances[$employee->id][$site->id][$d]['normal_night_hours'] ?? 0;
                    $otDayHours += $attendances[$employee->id][$site->id][$d]['ot_day_hours'] ?? 0;
                    $otNightHours += $attendances[$employee->id][$site->id][$d]['ot_night_hours'] ?? 0;
                }

                $totalSiteHours = $normalDayHours + $normalNightHours + $otDayHours + $otNightHours;
                $siteShifts = $totalSiteHours / 12;
                $siteEarning = ($totalSiteHours / 12) * $shiftRate;

                $totalShiftEarning += $siteEarning;
                $totalOTHours += $otDayHours + $otNightHours;
                $totalShifts += $siteShifts;
            }

            // Rates and performance allowance
            $otRate = round(($employee->basic / 240 * 1.5), 2);
            $otEarnings = round($otRate * $paidOtHours, 2);
            $performanceAllowance = round($otRate * $performanceOtHours, 2);

            $subTotal = $employee->basic + $employee->attendance_bonus + $otEarnings + $performanceAllowance;
            $otherAllowances = max(round($totalShiftEarning - $subTotal, 2), 0);
            $grossPay = $specialOtEarnings + $totalShiftEarning ;
            $epfDeductEmployee =  $employee->include_epf_etf ? ($employee->basic / 100) * 8 : 0;
            $totalDeductions = $epfDeductEmployee + $totalSalaryAdvance + $mealDeductions + $uniformDeductions;
            $epfEmployee =  $employee->include_epf_etf ? ($employee->basic / 100) * 12 : 0;
            $etfEmployee =  $employee->include_epf_etf ? ($employee->basic / 100) * 3 : 0;
            $salaryData[] = [
                'employee' => $employee,
                'total_shifts' => $totalShifts,
                'basic' => $employee->basic,
                'ot_earnings' => $otEarnings,
                'performance_allowance' => $performanceAllowance,
                'special_ot_earnings' => $specialOtEarnings,
                'totalShiftEarning' => $totalShiftEarning,
                'attendance_bonus' => $employee->attendance_bonus,
                'other_allowances' => $otherAllowances,
                'sub_total' => $subTotal,
                'gross_pay' => $grossPay,
                'salary_advance' => $totalSalaryAdvance,
                'meal_deductions' => $mealDeductions,
                'uniform_deductions' => $uniformDeductions,
                'epf_deduct_employee' => $epfDeductEmployee,
                'total_deductions' => $totalDeductions,
                'net_pay' => $grossPay - $totalDeductions,
                'epf_employee' => $epfEmployee,
                'etf_employee' => $etfEmployee,
                'ot_days' => $otDays,
                'paid_ot_hours' => $paidOtHours,
                'performance_ot_hours' => $performanceOtHours,
            ];
        }

        return $salaryData;
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


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Salary $salary)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSalaryRequest $request, Salary $salary)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Salary $salary)
    {
        //
    }
}
