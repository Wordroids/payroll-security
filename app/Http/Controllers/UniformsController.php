<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Uniform;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UniformsController extends Controller
{

    //Index table
    public function index(Request $request)
    {
        $currentMonth = $request->input('month');
        $employeeId = $request->input('employee_id');
        $type = $request->input('type');
        $showAll = $request->input('show_all', false);

        // Get employees with their uniforms
        $query = Employee::with(['uniforms' => function ($query) use ($currentMonth, $showAll, $type) {
            if (!$showAll && $currentMonth) {
                $query->whereMonth('date', '=', substr($currentMonth, 5, 2))
                    ->whereYear('date', '=', substr($currentMonth, 0, 4));
            }
            if ($type) {
                $query->where('type', $type);
            }
        }])->whereHas('uniforms', function ($query) use ($currentMonth, $showAll, $type) {
            if (!$showAll && $currentMonth) {
                $query->whereMonth('date', '=', substr($currentMonth, 5, 2))
                    ->whereYear('date', '=', substr($currentMonth, 0, 4));
            }
            if ($type) {
                $query->where('type', $type);
            }
        });

        if ($employeeId) {
            $query->where('id', $employeeId);
        }

        $employees = $query->orderBy('name')->paginate(10);
        $allEmployees = Employee::orderBy('name')->get();

        // Calculate total uniform costs
        $totalQuery = Uniform::query();
        if (!$showAll && $currentMonth) {
            $totalQuery->whereMonth('date', '=', substr($currentMonth, 5, 2))
                ->whereYear('date', '=', substr($currentMonth, 0, 4));
        }
        if ($employeeId) {
            $totalQuery->where('employee_id', $employeeId);
        }
        if ($type) {
            $totalQuery->where('type', $type);
        }
        $totalUniformCosts = $totalQuery->sum('total_amount');

        return view('pages.uniforms.index', [
            'employees' => $employees,
            'allEmployees' => $allEmployees,
            'currentMonth' => $currentMonth,
            'employeeId' => $employeeId,
            'type' => $type,
            'totalUniformCosts' => $totalUniformCosts,
            'showAll' => $showAll
        ]);
    }

    //to create a new uniform record
    public function create()
    {
        $employees = Employee::orderBy('name')->get();
        $uniformTypes = ['Shirt', 'Trouser', 'Belt', 'Apalo', 'Lanyard', 'Shoes'];
        return view('pages.uniforms.create', compact('employees', 'uniformTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'type' => 'required|string',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
            'notes' => 'nullable|string'
        ]);

        $totalAmount = $validated['unit_price'] * $validated['quantity'];

        Uniform::create([
            'employee_id' => $validated['employee_id'],
            'date' => $validated['date'],
            'type' => $validated['type'],
            'quantity' => $validated['quantity'],
            'unit_price' => $validated['unit_price'],
            'total_amount' => $totalAmount,
            'notes' => $validated['notes'] ?? null
        ]);

        return redirect()->route('uniforms.index')
            ->with('success', 'Uniform record added successfully.');
    }
    //edit uniforms
    public function editEmployeeUniforms($employeeId, Request $request)
    {
        try {
            $month = $request->input('month');
            $showAll = !$request->has('month');

            $employee = Employee::findOrFail($employeeId);

            $query = $employee->uniforms()->orderBy('date', 'desc');

            if (!$showAll && $month) {
                $query->whereYear('date', '=', substr($month, 0, 4))
                    ->whereMonth('date', '=', substr($month, 5, 2));
            }

            $uniforms = $query->get();

            return view('pages.uniforms.edit', [
                'employee' => $employee,
                'uniforms' => $uniforms,
                'month' => $month,
                'showAll' => $showAll
            ]);
        } catch (\Exception $e) {
            Log::error("Error in editEmployeeUniforms method: " . $e->getMessage());
            return redirect()->back()->with('error', 'Error loading uniform details');
        }
    }

    public function edit($id)
    {
        try {
            $uniform = Uniform::with('employee')->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => [
                    'date' => $uniform->date->format('Y-m-d'),
                    'type' => $uniform->type,
                    'quantity' => $uniform->quantity,
                    'unit_price' => $uniform->unit_price,
                    'notes' => $uniform->notes ?? '',
                    'employee' => $uniform->employee
                ]
            ]);
        } catch (\Exception $e) {
            Log::error("Error fetching uniform: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load uniform details'
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $uniform = Uniform::findOrFail($id);
            $uniform->delete();

            return response()->json([
                'success' => true,
                'message' => 'Uniform record deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error("Error deleting uniform: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete uniform record'
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'date' => 'required|date',
                'type' => 'required|string',
                'quantity' => 'required|integer|min:1',
                'unit_price' => 'required|numeric|min:0',
                'notes' => 'nullable|string'
            ]);

            $totalAmount = $validated['unit_price'] * $validated['quantity'];

            $uniform = Uniform::findOrFail($id);
            $uniform->update([
                'date' => $validated['date'],
                'type' => $validated['type'],
                'quantity' => $validated['quantity'],
                'unit_price' => $validated['unit_price'],
                'total_amount' => $totalAmount,
                'notes' => $validated['notes'] ?? null
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Uniform record updated successfully',
                'data' => $uniform
            ]);
        } catch (\Exception $e) {
            Log::error("Error updating uniform: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Error updating uniform record',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
