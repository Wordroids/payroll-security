<?php
namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Site;
use App\Models\Attendance;
use App\Models\SalaryAdvance;
use App\Models\Meals;
use App\Models\Uniform;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $now = Carbon::now();
        $lastMonth = Carbon::now()->subMonth();
        $lastMonthSalary = $this->calculateLastMonthNetPay($lastMonth);

        return view('dashboard', [
            'totalEmployees' => Employee::count(),
            'totalSites' => Site::count(),
            'lastMonthSalary' => $lastMonthSalary,
            'lastMonthAdvance' => SalaryAdvance::whereYear('advance_date', $lastMonth->year)
                                             ->whereMonth('advance_date', $lastMonth->month)
                                             ->sum('amount'),
        ]);
    }

    private function calculateLastMonthNetPay($lastMonth)
    {
        $month = $lastMonth->format('Y-m');
        $startDate = $lastMonth->copy()->startOfMonth();
        $endDate = $lastMonth->copy()->endOfMonth();
        $prevMonth = $lastMonth->copy()->subMonth()->format('Y-m');

        $employees = Employee::all();
        $totalNetPay = 0;

        foreach ($employees as $employee) {
            try {
                //  attendance records for the last month
                $records = Attendance::whereBetween('date', [$startDate, $endDate])
                    ->where('employee_id', $employee->id)
                    ->get();

                if ($records->isEmpty()) {
                    continue;
                }

                $netPay = $this->calculateEmployeeNetPay($employee, $month, $startDate, $endDate, $prevMonth);

                if ($netPay > 0) {
                    $totalNetPay += $netPay;
                }

            } catch (\Exception $e) {
                \Log::error("Error calculating net pay for employee {$employee->id}: " . $e->getMessage());
                continue;
            }
        }

        return $totalNetPay;
    }

    private function calculateEmployeeNetPay($employee, $month, $startDate, $endDate, $prevMonth)
    {
        $employee->load(['sites', 'salaryAdvances']);

        // Get attendance
        $records = Attendance::whereBetween('date', [$startDate, $endDate])
            ->where('employee_id', $employee->id)
            ->get();

        $attendances = [];
        $specialOtEarnings = 0;

        foreach ($records as $attendance) {
            $day = Carbon::parse($attendance->date)->day;
            $attendances[$attendance->employee_id][$attendance->site_id][$day][$attendance->shift] = $attendance->worked_hours;
        }

        // Process normal/OT hours
        foreach ($attendances as $empId => $sites) {
            foreach ($sites as $siteId => $days) {
              //  $site = Site::find($siteId);
                $site = $employee->sites->find($siteId);
                if (!$site) continue;

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
                            $specialOtEarnings += $specialOtDay * 200; // Fixed rate
                        }
                    }
                    if (isset($shifts['night'])) {
                        $nightHours = $shifts['night'];
                        $attendances[$empId][$siteId][$day]['normal_night_hours'] = min($nightHours, 9);
                        $attendances[$empId][$siteId][$day]['ot_night_hours'] = min(max($nightHours - 9, 0), 3);
                        if ($site->has_special_ot_hours) {
                            $specialOtNight = max($nightHours - 12, 0);
                            $specialOtEarnings += $specialOtNight * 200; 
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

        // Calculate per-site earnings
        $totalShiftEarning = 0;
        $totalShifts = 0;

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
            $totalShifts += $siteShifts;
        }

        // OT rate and performance allowance
        $otRate = round(($employee->basic / 240 * 1.5), 2);
        $otEarnings = round($otRate * $paidOtHours, 2);
        $performanceAllowance = round($otRate * $performanceOtHours, 2);

        $subTotal = $employee->basic + $employee->attendance_bonus + $otEarnings + $performanceAllowance;
        $otherAllowances = max(round($totalShiftEarning - $subTotal, 2), 0);

        // Gross pay
        $grossPay = $specialOtEarnings + $totalShiftEarning;

        // EPF deductions
        $epfDeductEmployee = $employee->include_epf_etf ? ($employee->basic / 100) * 8 : 0;

        // Total deductions
        $totalDeductions = $epfDeductEmployee + $totalSalaryAdvance + $mealDeductions + $uniformDeductions;

        // NET PAY
        $netPay = $grossPay - $totalDeductions;

        return $netPay;
    }
}
