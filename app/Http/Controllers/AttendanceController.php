<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAttendanceRequest;
use App\Http\Requests\UpdateAttendanceRequest;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Site;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $month = $request->input('month', now()->format('Y-m')); // default: current month
        $employeeId = $request->input('employee_id');

        $startDate = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $endDate = Carbon::createFromFormat('Y-m', $month)->endOfMonth();


        // Get all employees (for dropdown)
        $allEmployees = Employee::orderBy('name')->get();

        // Load Sites with filtered employees
        $sites = Site::with(['employees' => function ($q) use ($employeeId) {
            if ($employeeId) {
                $q->where('employee_id', $employeeId);
            }
        }])->get();


        // Fetch attendances
        $records = Attendance::whereBetween('date', [$startDate, $endDate])->when($employeeId, fn($q) => $q->where('employee_id', $employeeId))->get();


        $attendances = [];

        foreach ($records as $attendance) {
            $day = Carbon::parse($attendance->date)->day;
            $attendances[$attendance->employee_id][$attendance->site_id][$day][$attendance->shift] = $attendance->worked_hours;
        }

        foreach ($attendances as $empId => $sitess) {

            foreach ($sitess as $siteId => $days) {
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

       
        return view('pages.attendances.index', [
            'month' => $month,
            'employeeId' => $employeeId,
            'attendances' => $attendances,
            'allEmployees' => $allEmployees,
            'sites' => $sites,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    public function siteEntryForm(Request $request)
    {
        $sites = Site::all();
        $selectedSite = $request->site_id ?? null;
        $selectedMonth = $request->month ?? now()->format('Y-m'); // e.g., 2024-04

        $guards = [];
        $filledAttendances = [];
        if ($selectedSite) {
            $guards = Site::find($selectedSite)?->employees ?? [];
            $startDate = Carbon::createFromFormat('Y-m', $selectedMonth)->startOfMonth();
            $endDate = Carbon::createFromFormat('Y-m', $selectedMonth)->endOfMonth();

            $records = Attendance::whereBetween('date', [$startDate, $endDate])
                ->whereIn('employee_id', $guards->pluck('id'))
                ->where('site_id', $selectedSite)
                ->get();



            foreach ($records as $record) {
                $day = Carbon::parse($record->date)->day;
                $filledAttendances[$record->employee_id][$day][$record->shift] = $record->worked_hours;
            }
        }

        return view('pages.attendances.site-entry', compact('sites', 'guards', 'selectedSite', 'selectedMonth', 'filledAttendances'));
    }

    public function storeSiteEntry(Request $request)
    {
        $siteId = $request->input('site_id');
        $month = $request->input('month'); // e.g. 2024-04
        $data = $request->input('attendances', []);

        foreach ($data as $employeeId => $days) {
            foreach ($days as $day => $shifts) {
                $date = Carbon::createFromFormat('Y-m-d', "{$month}-" . str_pad($day, 2, '0', STR_PAD_LEFT));

                foreach (['day', 'night'] as $shift) {
                    $workedHours = $shifts[$shift] ?? null;

                    if ($workedHours !== null && $workedHours !== '') {
                        Attendance::updateOrCreate(
                            [
                                'employee_id' => $employeeId,
                                'site_id' => $siteId,
                                'date' => $date,
                                'shift' => $shift,
                            ],
                            ['worked_hours' => $workedHours]
                        );
                    }
                }
            }
        }

        return redirect()->back()->with('success', 'Day & Night attendance successfully saved.');
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAttendanceRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Attendance $attendance)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Attendance $attendance)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAttendanceRequest $request, Attendance $attendance)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Attendance $attendance)
    {
        //
    }
}
