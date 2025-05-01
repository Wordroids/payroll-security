<?php

namespace App\Http\Controllers;

use App\Models\Salary;
use App\Http\Requests\StoreSalaryRequest;
use App\Http\Requests\UpdateSalaryRequest;
use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;

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
    
        // Get attendance
        $records = Attendance::whereBetween('date', [$startDate, $endDate])
            ->where('employee_id', $employee->id)
            ->get();
    
        $attendances = [];
    
        foreach ($records as $attendance) {
            $day = Carbon::parse($attendance->date)->day;
            $attendances[$attendance->employee_id][$attendance->site_id][$day][$attendance->shift] = $attendance->worked_hours;
        }
    
        // Process normal/OT hours
        foreach ($attendances as $empId => $sites) {
            foreach ($sites as $siteId => $days) {
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
            $dayTotal = 0;
            $nightTotal = 0;
            $normalDayHours = 0;
            $normalNightHours = 0;
            $otDayHours = 0;
            $otNightHours = 0;
    
            for ($d = 1; $d <= $startDate->daysInMonth; $d++) {
                $dayHours = $attendances[$employee->id][$site->id][$d]['day'] ?? 0;
                $nightHours = $attendances[$employee->id][$site->id][$d]['night'] ?? 0;
                $dayTotal += $dayHours;
                $nightTotal += $nightHours;
    
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
    
            $siteSummaries[] = [
                'site' => $site,
                'shifts' => $siteShifts,
                'earning' => $siteEarning,
            ];
        }
    
        // Rates
        $combinedBase = $employee->basic + $employee->br_allow;
        $otRate = round(((($combinedBase / 8) * 1.5) / 26), 2);
        $otEarnings = round($otRate * $totalOTHours, 2);
    
        $subTotal = $employee->basic + $employee->br_allow + $employee->attendance_bonus + $otEarnings;
        $otherAllowances = round($totalShiftEarning - $subTotal, 2);
    
        $grossPay = $totalShiftEarning;
        $epfEmployee = ($combinedBase / 100) * 8;
        $epfEtfEmployer = ($employee->basic / 100) * 15;
        $totalDeductions = $epfEmployee + $employee->totalSalaryAdvance;
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
            'totalDeductions',
            'totalEarnings'
        ));
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
