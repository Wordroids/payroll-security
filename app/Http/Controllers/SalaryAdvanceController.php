<?php

namespace App\Http\Controllers;

use App\Models\SalaryAdvance;
use App\Http\Requests\StoreSalaryAdvanceRequest;
use App\Http\Requests\UpdateSalaryAdvanceRequest;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SalaryAdvanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function salaryAdvance(Request $request)
    {
        $currentMonth = $request->input('month');
        $showAll = $request->input('show_all', false);

        // Employees with salary advances
        $query = Employee::with(['salaryAdvances' => function ($query) use ($currentMonth, $showAll) {
            if (!$showAll && $currentMonth) {
                $query->where('advance_date', 'like', $currentMonth . '%');
            }
        }])
            ->whereHas('salaryAdvances');

        // If filtering by month
        if (!$showAll && $currentMonth) {
            $query->whereHas('salaryAdvances', function ($query) use ($currentMonth) {
                $query->where('advance_date', 'like', $currentMonth . '%');
            });
        }

        $employees = $query->orderBy('emp_no')->paginate(10);

        // Calculate total advances
        $totalQuery = SalaryAdvance::query();
        if (!$showAll && $currentMonth) {
            $totalQuery->where('advance_date', 'like', $currentMonth . '%');
        }
        $totalSalaryAdvances = $totalQuery->sum('amount');

        return view('pages.salaries.advances', [
            'employees' => $employees,
            'currentMonth' => $currentMonth,
            'totalSalaryAdvances' => $totalSalaryAdvances,
            'showAll' => $showAll
        ]);
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
     * Edit all advances for an employee in a specific month
     */
    public function edit($employeeId, Request $request)
    {
        try {
            // Get the month filter if provided
            $month = $request->input('month');
            $showAll = !$request->has('month'); // Show all if no month filter

            // Get the employee with advances
            $employee = Employee::with(['salaryAdvances' => function ($query) use ($month, $showAll) {
                if (!$showAll && $month) {
                    $query->whereYear('advance_date', '=', substr($month, 0, 4))
                        ->whereMonth('advance_date', '=', substr($month, 5, 2));
                }
                $query->orderBy('advance_date');
            }])->findOrFail($employeeId);

            return view('pages.salaries.editAdvance', [
                'employee' => $employee,
                'advances' => $employee->salaryAdvances,
                'month' => $month,
                'showAll' => $showAll
            ]);
        } catch (\Exception $e) {
            Log::error("Error in edit method: " . $e->getMessage());
            return redirect()->back()->with('error', 'Error loading advance details');
        }
    }
    /**
     * Get single advance details for editing
     */
    public function editSingle($id)
    {
        try {
            $advance = SalaryAdvance::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => [
                    'amount' => $advance->amount,
                    'advance_date' => $advance->advance_date,
                    'reason' => $advance->reason ?? ''
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Advance not found',
                'message' => $e->getMessage()
            ], 404);
        }
    }
    //to update
    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'amount' => 'required|numeric|min:0',
                'advance_date' => 'required|date',
                'reason' => 'nullable|string|max:255',
            ]);

            $advance = SalaryAdvance::findOrFail($id);
            $advance->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Advance updated successfully'
            ]);
        } catch (\Exception $e) {
            Log::error("Error updating advance: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Error updating advance',
                'message' => $e->getMessage()
            ], 500);
        }
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
