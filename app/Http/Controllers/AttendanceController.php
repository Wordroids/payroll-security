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

        $employees = Employee::orderBy('name')->get();

        $records = Attendance::whereBetween('date', [$startDate, $endDate])
            ->when($employeeId, fn($q) => $q->where('employee_id', $employeeId))
            ->get();

        $attendances = [];

        foreach ($records as $attendance) {
            $day = Carbon::parse($attendance->date)->day;
            $attendances[$attendance->employee_id][$day][$attendance->shift] = $attendance->worked_hours;
        }

        return view('pages.attendances.index', compact('month', 'employeeId', 'attendances', 'employees'));
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
        if ($selectedSite) {
            $guards = Site::find($selectedSite)?->employees ?? [];
        }

        return view('pages.attendances.site-entry', compact('sites', 'guards', 'selectedSite', 'selectedMonth'));
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
