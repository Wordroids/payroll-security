<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SalarySetting;
use Carbon\Carbon;

class SalarySettingController extends Controller
{
    public function index()
    {

        $settings = SalarySetting::orderBy('applicable_year', 'desc')
            ->orderBy('applicable_month', 'desc')
            ->first();

        return view('pages.salary-settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'applicable_date' => 'required|date_format:Y-m',
            'default_basic_salary' => 'required|numeric|min:0',
            'default_attendance_bonus' => 'required|numeric|min:0',
            'special_ot_rate' => 'required|numeric|min:0',
        ]);


        $date = Carbon::parse($request->applicable_date);
        $year = $date->year;
        $month = $date->month;

        
        SalarySetting::updateOrCreate(
            [
                'applicable_year' => $year,
                'applicable_month' => $month
            ],
            [
                'default_basic_salary' => $request->default_basic_salary,
                'default_attendance_bonus' => $request->default_attendance_bonus,
                'special_ot_rate' => $request->special_ot_rate,
            ]
        );

        return redirect()->route('salary-settings.index')
            ->with('success', 'Salary settings updated for ' . $request->applicable_date);
    }
}
