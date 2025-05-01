<?php
namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Site;
use App\Models\Attendance;
use App\Models\SalaryAdvance;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $now = Carbon::now();

        return view('dashboard', [
            'totalEmployees' => Employee::count(),
            'totalSites' => Site::count(),
            'monthlyAttendances' => Attendance::whereMonth('date', $now->month)->count(),
            'monthlyAdvance' => SalaryAdvance::whereMonth('advance_date', $now->month)->sum('amount'),
        ]);
    }
}
