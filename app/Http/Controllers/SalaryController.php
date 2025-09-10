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



        // Process normal/OT hours
        foreach ($attendances as $empId => $sites) {
            foreach ($sites as $siteId => $days) {
                $site = Site::find($siteId);
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
                            // $specialOtEarnings += $specialOtDay * $site->special_ot_rate;
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
                        }
                    }
                }
            }
        }

        $specialOtHours = $specialOtDayHours + $specialOtNightHours;
        // special OT rate: guard_shift_rate/12*1.5
        $firstSite = $employee->sites->first();
        $guardShiftRate = $firstSite ? $firstSite->guard_shift_rate : 0;
        $specialOtRate = round(($guardShiftRate / 12 * 1.5), 2);
        $specialOtEarnings = $specialOtHours * $specialOtRate;

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
        //  OT rate: 30000/240*1.5
        $otRate = round(($employee->basic / 240 * 1.5), 2);
        $otEarnings = round($otRate * $totalOTHours, 2);
        $subTotal = $employee->basic + $employee->attendance_bonus + $otEarnings;
        $otherAllowances = max(round($totalShiftEarning - $subTotal, 2), 0);
        // gross pay
        $grossPay = $specialOtEarnings + $employee->basic + $otherAllowances + $otEarnings;
        $epfEmployee =  $employee->include_epf_etf ?($employee->basic / 100) * 12:0;
        $etfEmployee =  $employee->include_epf_etf ?($employee->basic / 100) * 3:0;
        $epfEtfEmployer =  $employee->include_epf_etf ?($employee->basic / 100) * 15:0;
        $totalDeductions = $epfEmployee + $employee->totalSalaryAdvance + $mealDeductions + $uniformDeductions;
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

            foreach ($records as $attendance) {
                $day = Carbon::parse($attendance->date)->day;
                $attendances[$attendance->employee_id][$attendance->site_id][$day][$attendance->shift] = $attendance->worked_hours;
            }
            // Process normal/OT hours
            foreach ($attendances as $empId => $sites) {
                foreach ($sites as $siteId => $days) {
                    $site = Site::find($siteId);
                        foreach ($days as $day => $shifts) {
                            if (isset($shifts['day'])) {
                           $dayHours = $shifts['day'];
                            $attendances[$empId][$siteId][$day]['normal_day_hours'] = min($dayHours, 9);
                                $attendances[$empId][$siteId][$day]['ot_day_hours'] = min(max($dayHours - 9, 0), 3);

                            if ($site->has_special_ot_hours) {
                                $specialOtHours += max($dayHours - 12, 0);
                            }
                            }
                            if (isset($shifts['night'])) {
                            $nightHours = $shifts['night'];
                             $attendances[$empId][$siteId][$day]['normal_night_hours'] = min($nightHours, 9);
                            $attendances[$empId][$siteId][$day]['ot_night_hours'] = min(max($nightHours - 9, 0), 3);
                            if ($site->has_special_ot_hours) {
                                $specialOtHours += max($nightHours - 12, 0);
                            }
                        }
                    }
                }
            }

            //  special OT rate: guard_shift_rate/12*1.5
            $firstSite = $employee->sites->first();
            $guardShiftRate = $firstSite ? $firstSite->guard_shift_rate : 0;
            $specialOtRate = round(($guardShiftRate / 12 * 1.5), 2);
            $specialOtEarnings = $specialOtHours * $specialOtRate;

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

                for ($d = 1; $d <= $startDate->daysInMonth; $d++) {
                    $normalDayHours += $attendances[$employee->id][$site->id][$d]['normal_day_hours'] ?? 0;
                    $normalNightHours += $attendances[$employee->id][$site->id][$d]['normal_night_hours'] ?? 0;
                    $otDayHours += $attendances[$employee->id][$site->id][$d]['ot_day_hours'] ?? 0;
                    $otNightHours += $attendances[$employee->id][$site->id][$d]['ot_night_hours'] ?? 0;
                }

                $totalSiteHours = $normalDayHours + $normalNightHours + $otDayHours + $otNightHours;
                $siteShifts = $totalSiteHours / 12;
                $siteEarning = ($totalSiteHours / 12) * $site->guard_shift_rate;

                $totalShiftEarning += $siteEarning;
                $totalOTHours += $otDayHours + $otNightHours;
                $totalShifts += $siteShifts;
            }

            // OT rate: 30000/240*1.5
            $otRate = round(($employee->basic / 240 * 1.5), 2);
            $otEarnings = round($otRate * $totalOTHours, 2);
            $subTotal = $employee->basic + $employee->attendance_bonus + $otEarnings;
            $otherAllowances = max(round($totalShiftEarning - $subTotal, 2), 0);

            // gross pay
            $grossPay = $specialOtEarnings + $employee->basic + $otherAllowances + $otEarnings;
            $epfEmployee =  $employee->include_epf_etf ?($employee->basic / 100) * 12:0;
            $etfEmployee =  $employee->include_epf_etf ?($employee->basic / 100) * 3:0;
            $totalDeductions = $epfEmployee + $totalSalaryAdvance + $mealDeductions + $uniformDeductions;
            $salaryData[] = [
                'employee' => $employee,
                'total_shifts' => $totalShifts,
                'basic' => $employee->basic,
                'ot_earnings' => $otEarnings,
                'special_ot_earnings' => $specialOtEarnings,
                'attendance_bonus' => $employee->attendance_bonus,
                'other_allowances' => $otherAllowances,
                'sub_total' => $subTotal,
                'gross_pay' => $grossPay,
                'epf_employee' => $epfEmployee,
                'etf_employee' => $etfEmployee,
                'salary_advance' => $totalSalaryAdvance,
                'meal_deductions' => $mealDeductions,
                'uniform_deductions' => $uniformDeductions,
                'total_deductions' => $totalDeductions,
                'net_pay' => $grossPay - $totalDeductions,
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
                'specialOtHours' => 0,
                'specialOtEarnings' => 0,
                'otherAllowances' => 0,
                'grossPay' => 0,
                'epfEmployee' => 0,
                'etfEmployee' => 0,
                'totalDeductions' => 0,
                'totalEarnings' => 0,
                'siteSummaries' => [],
                'otRate' => 0,
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
            //  $data['specialOtEarnings'] = 0;

        foreach ($records as $attendance) {
            $day = Carbon::parse($attendance->date)->day;
            $attendances[$attendance->employee_id][$attendance->site_id][$day][$attendance->shift] = $attendance->worked_hours;
        }

        // Process attendance data
        foreach ($attendances as $empId => $sites) {
            foreach ($sites as $siteId => $days) {
                $site = Site::find($siteId);
                    foreach ($days as $day => $shifts) {
                        if (isset($shifts['day'])) {
                            $dayHours = $shifts['day'];
                            $attendances[$empId][$siteId][$day]['normal_day_hours'] = min($dayHours, 9);
                            $attendances[$empId][$siteId][$day]['ot_day_hours'] = min(max($dayHours  - 9, 0), 3);

                            if ($site->has_special_ot_hours) {
                                $specialOtDay = max($dayHours - 12, 0);
                            $specialOtDayHours += $specialOtDay;
                        }
                        }

                        if (isset($shifts['night'])) {
                            $nightHours = $shifts['night'];
                            $attendances[$empId][$siteId][$day]['normal_night_hours'] = min($nightHours, 9);
                            $attendances[$empId][$siteId][$day]['ot_night_hours'] = min(max($nightHours - 9, 0), 3);

                            if ($site->has_special_ot_hours) {
                                $specialOtNight = max($nightHours - 12, 0);
                                $specialOtNightHours += $specialOtNight;
                            }
                        }
                    }
                }
            }

            $data['specialOtHours'] = $specialOtDayHours + $specialOtNightHours;
            //  special OT rate: guard_shift_rate/12*1.5
            $firstSite = $employee->sites->first();
            $guardShiftRate = $firstSite ? $firstSite->guard_shift_rate : 0;
            $data['specialOtRate'] = round(($guardShiftRate / 12 * 1.5), 2);
            $data['specialOtEarnings'] = $data['specialOtHours'] * $data['specialOtRate'];

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

        // OT rate: 30000/240*1.5
        $otRate = round(($employee->basic / 240 * 1.5), 2);
        $data['otEarnings'] = round($data['otRate'] * $data['totalOTHours'], 2);
        $subTotal = $data['basic'] + $data['attendance_bonus'] + $data['otEarnings'];
        $data['otherAllowances'] = max(round($data['totalShiftEarning'] - $subTotal, 2), 0);
        // gross pay
        $data['grossPay'] = $data['specialOtEarnings'] + $data['basic'] + $data['otherAllowances'] + $data['otEarnings'];
        $data['epfEmployee'] = $employee->include_epf_etf ? ($employee->basic / 100) * 12:0;
        $data['totalDeductions'] = $data['epfEmployee'] + $data['totalSalaryAdvance'] + $data['mealDeductions'] + $data['uniformDeductions'];
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

            foreach ($records as $attendance) {
                $day = Carbon::parse($attendance->date)->day;
                $attendances[$attendance->employee_id][$attendance->site_id][$day][$attendance->shift] = $attendance->worked_hours;
            }

            // Process normal/OT hours
            foreach ($attendances as $empId => $sites) {
                foreach ($sites as $siteId => $days) {
                    $site = Site::find($siteId);
                    if ($site->has_special_ot_hours) {
                        foreach ($days as $day => $shifts) {
                            if (isset($shifts['day'])) {
                                $attendances[$empId][$siteId][$day]['normal_day_hours'] = min($shifts['day'], 9);
                                $attendances[$empId][$siteId][$day]['ot_day_hours'] = min(max($shifts['day'] - 9, 0), 3);
                            }
                            if (isset($shifts['night'])) {
                                $attendances[$empId][$siteId][$day]['normal_night_hours'] = min($shifts['night'], 9);
                                $attendances[$empId][$siteId][$day]['ot_night_hours'] = min(max($shifts['night'] - 9, 0), 3);
                            }
                            // special ot total
                            if (isset($shifts['day'])) {
                                $specialOtHours += max($shifts['day'] - 12, 0);
                            }
                        }
                    } else {
                        foreach ($days as $day => $shifts) {
                            if (isset($shifts['day'])) {
                                $attendances[$empId][$siteId][$day]['normal_day_hours'] = min($shifts['day'], 9);
                                $attendances[$empId][$siteId][$day]['ot_day_hours'] = max($shifts['day'] - 9, 0);
                            }
                            if (isset($shifts['night'])) {
                                $attendances[$empId][$siteId][$day]['normal_night_hours'] = min($shifts['night'], 9);
                                $attendances[$empId][$siteId][$day]['ot_night_hours'] = max($shifts['night'] - 9, 0);
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

                for ($d = 1; $d <= $startDate->daysInMonth; $d++) {
                    $normalDayHours += $attendances[$employee->id][$site->id][$d]['normal_day_hours'] ?? 0;
                    $normalNightHours += $attendances[$employee->id][$site->id][$d]['normal_night_hours'] ?? 0;
                    $otDayHours += $attendances[$employee->id][$site->id][$d]['ot_day_hours'] ?? 0;
                    $otNightHours += $attendances[$employee->id][$site->id][$d]['ot_night_hours'] ?? 0;
                }

                $totalSiteHours = $normalDayHours + $normalNightHours + $otDayHours + $otNightHours;
                $siteShifts = $totalSiteHours / 12;
                $siteEarning = ($totalSiteHours / 12) * $site->guard_shift_rate;

                $totalShiftEarning += $siteEarning;
                $totalOTHours += $otDayHours + $otNightHours;
                $totalShifts += $siteShifts;
            }

            // Rates
            $combinedBase = $employee->basic ;
            $otRate = round(((($combinedBase / 9) * 1.5) / 26), 2);
            $otEarnings = round($otRate * $totalOTHours, 2);

            $subTotal = $employee->basic + $employee->attendance_bonus + $otEarnings;
            $otherAllowances = max(round($totalShiftEarning - $subTotal, 2), 0);
            $grossPay = $totalShiftEarning + $otEarnings + ($specialOtHours * 200);
            $epfEmployee =  $employee->include_epf_etf ?($employee->basic / 100) * 12:0;
            $etfEmployee =  $employee->include_epf_etf ?($employee->basic / 100) * 3:0;
            $totalDeductions = $epfEmployee + $totalSalaryAdvance + $mealDeductions + $uniformDeductions;

            $salaryData[] = [
                'employee' => $employee,
                'total_shifts' => $totalShifts,
                'basic' => $employee->basic,
                'ot_earnings' => $otEarnings,
                'attendance_bonus' => $employee->attendance_bonus,
                'other_allowances' => $otherAllowances,
                'sub_total' => $subTotal,
                'gross_pay' => $grossPay,
                'epf_employee' => $epfEmployee,
                'etf_employee' => $etfEmployee,
                'salary_advance' => $totalSalaryAdvance,
                'meal_deductions' => $mealDeductions,
                'uniform_deductions' => $uniformDeductions,
                'total_deductions' => $totalDeductions,
                'net_pay' => $grossPay - $totalDeductions,
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
