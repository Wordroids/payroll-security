<?php

namespace App\Http\Controllers;

use App\Models\SalaryAdvance;
use App\Http\Requests\StoreSalaryAdvanceRequest;
use App\Http\Requests\UpdateSalaryAdvanceRequest;
use App\Models\Employee;
use Illuminate\Http\Request;

class SalaryAdvanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function salaryAdvance()
    {
        $salaryAdvances = SalaryAdvance::with('employee')->get();
        return view('pages.salaries.advances', compact('salaryAdvances'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $employees = Employee::orderBy('emp_no')->get();
        return view('pages.salaries.create', compact('employees'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'advance_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'reason' => 'nullable|string|max:255',
        ]);
        SalaryAdvance::create($validated);
        return redirect()->route('salary.advance')
            ->with('success', 'Salary advance created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(SalaryAdvance $salaryAdvance)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SalaryAdvance $salaryAdvance)
    {
        $employees = Employee::orderBy('emp_no')->get();

        return view('pages.salaries.editAdvance', compact('salaryAdvance', 'employees'));
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SalaryAdvance $salaryAdvance)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'advance_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'reason' => 'nullable|string|max:255',
        ]);

        $salaryAdvance->update($validated);

        return redirect()->route('salary.advance')
            ->with('success', 'Salary advance updated successfully.');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SalaryAdvance $salaryAdvance)
    {
        $salaryAdvance->delete();

        return redirect()->route('salary.advance')
            ->with('success', 'Salary advance deleted successfully.');
    }
}
