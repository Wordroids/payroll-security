<?php
namespace App\Http\Controllers;

use App\Models\FixedSalary;
use Illuminate\Http\Request;

class FixedSalaryController extends Controller
{
    public function index()
    {
        $salaries = FixedSalary::orderBy('position')->get();
        return view('pages.fixed_salaries.index', compact('salaries'));
    }

    public function create()
    {
        $positions = ['MD', 'OM', 'VO', 'BDM', 'GM'];
        return view('pages.fixed_salaries.create', compact('positions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'position' => 'required|string',
            'employee_name' => 'required|string',
        ]);

        FixedSalary::create($request->all());

        return redirect()->route('fixed-salaries.index')->with('success', 'Fixed salary successfully added.');
    }

    public function edit(FixedSalary $fixedSalary)
    {
        $positions = ['MD', 'OM', 'VO', 'BDM', 'GM'];
        return view('pages.fixed_salaries.edit', compact('fixedSalary', 'positions'));
    }

    public function update(Request $request, FixedSalary $fixedSalary)
    {
        $fixedSalary->update($request->all());

        return redirect()->route('fixed-salaries.index')->with('success', 'Salary record updated successfully.');
    }

    public function destroy(FixedSalary $fixedSalary)
    {
        $fixedSalary->delete();
        return redirect()->route('fixed-salaries.index')->with('success', 'Salary record deleted.');
    }
}
