<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SalarySetting;

class SalarySettingController extends Controller
{
    public function index()
    {
        $settings = SalarySetting::getSettings();
        return view('pages.salary-settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'default_basic_salary' => 'required|numeric|min:0',
            'default_attendance_bonus' => 'required|numeric|min:0',

        ]);

        $settings = SalarySetting::getSettings();
        $settings->update($validated);

        return redirect()->route('salary-settings.index')
            ->with('success', 'Salary settings updated successfully.');
    }
}
