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
        $showAll = $request->input('show_all', false);
        $currentDate = $request->input('date', now()->format('Y-m-d'));
        $filterMonth = $request->input('month', null);
        // Employees with salary advances
        $query = Employee::with(['salaryAdvances' => function ($query) use ($currentDate, $filterMonth, $showAll) {
            if (!$showAll) {
                if ($filterMonth) {
                    $query->where('advance_date', 'like', $filterMonth . '%');
                } else {
                    $query->whereDate('advance_date', $currentDate);
                }
            }
        }]);

        // If filtering by month
        if (!$showAll) {
            $query->whereHas('salaryAdvances', function ($query) use ($currentDate, $filterMonth) {
                if ($filterMonth) {
                    $query->where('advance_date', 'like', $filterMonth . '%');
                } else {
                    $query->whereDate('advance_date', $currentDate);
                }
            });
        }

        $employees = $query->orderBy('emp_no')->paginate(10);

        // Calculate total advances
        $totalQuery = SalaryAdvance::query();
        if (!$showAll) {
            if ($filterMonth) {
                $totalQuery->where('advance_date', 'like', $filterMonth . '%');
            } else {
                $totalQuery->whereDate('advance_date', $currentDate);
            }
        }
        $totalSalaryAdvances = $totalQuery->sum('amount');

        return view('pages.salaries.advances', [
            'employees' => $employees,
            'currentMonth' => $filterMonth,
            'currentDate' => $currentDate,
            'filterMonth' => $filterMonth,
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
            $date = $request->input('date');
            $month = $request->input('month');
            $showAll = $request->boolean('show_all');
            $employee = Employee::with(['salaryAdvances' => function ($query) use ($date, $month, $showAll) {
                if (!$showAll) {
                    if ($month) {
                        $query->where('advance_date', 'like', $month . '%');
                    } elseif ($date) {
                        $query->whereDate('advance_date', $date);
                    } else {
                        $query->whereDate('advance_date', now()->format('Y-m-d'));
                    }
                }
                $query->orderBy('advance_date');
            }])->findOrFail($employeeId);

            return view('pages.salaries.editAdvance', [
                'employee' => $employee,
                'advances' => $employee->salaryAdvances,
                'date' => $date,
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
