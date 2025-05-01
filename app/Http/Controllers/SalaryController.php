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
    public function show(Employee $employee , Request $request)
    {
        //  dd($request->all());

        //load the employee site and salary advances
        $employee->load(['sites', 'salaryAdvances']);
        $month = $request->input('month', now()->format('Y-m')); // default: current month

        $startDate = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $endDate = Carbon::createFromFormat('Y-m', $month)->endOfMonth();

         // Fetch attendances
         $records = Attendance::whereBetween('date', [$startDate, $endDate])->when($employee->id, fn($q) => $q->where('employee_id', $employee->id))->get();
 

         $attendances = [];
     
         foreach ($records as $attendance) {
             $day = Carbon::parse($attendance->date)->day;
             $attendances[$attendance->employee_id][$attendance->site_id][$day][$attendance->shift] = $attendance->worked_hours;
         }
 
         foreach ($attendances as $empId => $sitess) {
 
             foreach($sitess as $siteId => $days) {
                 
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

         //get the total salary advances for this employee
            $salaryAdvances = $employee->salaryAdvances()->where('advance_date', '>=', $startDate)->where('advance_date', '<=', $endDate)->get();
            $totalSalaryAdvance = 0;
            foreach ($salaryAdvances as $advance) {
                $totalSalaryAdvance += $advance->amount;
            }
            $employee->totalSalaryAdvance = $totalSalaryAdvance;


        return view('pages.salaries.show-single-salary', compact('employee' , 'month' , 'attendances'));
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
