<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Meals;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MealsController extends Controller
{
    public function index(Request $request)
    {
        $employeeId = $request->input('employee_id');
        $showAll = $request->input('show_all', false);
        $currentDate = $request->input('date', now()->format('Y-m-d'));
        $filterMonth = $request->input('month', null);
        // Employees with meals
        $query = Employee::with(['meals' => function ($query) use ($currentDate, $filterMonth, $showAll) {
           if (!$showAll) {
                if ($filterMonth) {
                $query->where('date', 'like', $filterMonth . '%');
                } else {
                    $query->whereDate('date', $currentDate);
                }
            }
        }]);
        // filtering by month or date
        $query->whereHas('meals', function ($query) use ($currentDate, $filterMonth,$showAll) {
        if (!$showAll) {
            if ($filterMonth) {
                $query->where('date', 'like', $filterMonth . '%');
                } else {
                    $query->whereDate('date', $currentDate);
            }
                 }
        });
        // Filter by employee if specified
        if ($employeeId) {
            $query->where('id', $employeeId);
        }


        $employees = $query->orderBy('name')->paginate(10);
        $allEmployees = Employee::orderBy('name')->get();

        // Calculate total meal costs
        $totalQuery = Meals::query();
        if (!$showAll) {
            if ($filterMonth) {
            $totalQuery->where('date', 'like', $filterMonth . '%');
            } else {
                $totalQuery->whereDate('date', $currentDate);
            }
        }
        if ($employeeId) {
            $totalQuery->where('employee_id', $employeeId);
        }
        $totalMealCosts = $totalQuery->sum('total_amount');

        return view('pages.meals.index', [
            'employees' => $employees,
            'allEmployees' => $allEmployees,
            'currentDate' => $currentDate,
            'filterMonth' => $filterMonth,
            'employeeId' => $employeeId,
            'totalMealCosts' => $totalMealCosts,
            'showAll' => $showAll
        ]);
    }

    public function create()
    {
        $employees = Employee::orderBy('name')->get();
        return view('pages.meals.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'meal_items' => 'required|array|min:1',
            'meal_items.*.unit_price' => 'required|numeric|min:0',
            'meal_items.*.quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string'
        ]);

        $totalAmount = collect($validated['meal_items'])->sum(function($item) {
            return $item['unit_price'] * $item['quantity'];
        });

        Meals::create([
            'employee_id' => $validated['employee_id'],
            'date' => $validated['date'],
            'meal_items' => $validated['meal_items'],
            'total_amount' => $totalAmount,
            'notes' => $validated['notes'] ?? null
        ]);

        return redirect()->route('meals.index')
            ->with('success', 'Meal cost added successfully.');
    }

    public function editEmployeeMeals($employeeId, Request $request)
    {
        try {
            $date = $request->input('date');
            $month = $request->input('month');
            $showAll = $request->boolean('show_all');

            $employee = Employee::findOrFail($employeeId);
            $query = $employee->meals()->orderBy('date', 'desc');


             if (!$showAll) {
                if ($month) {
                $query->where('date', 'like', $month . '%');
                } elseif ($date) {
                    $query->whereDate('date', $date);
                } else {
                    $query->whereDate('date', now()->format('Y-m-d'));
                }
            }

            $meals = $query->get();

            return view('pages.meals.edit', [
                'employee' => $employee,
                'meals' => $meals,
                'date' => $date,
                'month' => $month,
                'showAll' => $showAll
            ]);
        } catch (\Exception $e) {
            Log::error("Error in editEmployeeMeals method: " . $e->getMessage());
            return redirect()->back()->with('error', 'Error loading meal details');
        }
    }

    public function edit($id)
{
    try {
        $meal = Meals::with('employee')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'date' => $meal->date,
                'meal_items' => $meal->meal_items,
                'notes' => $meal->notes ?? '',
                'employee' => $meal->employee
            ]
        ]);
    } catch (\Exception $e) {
        Log::error("Error fetching meal: " . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Failed to load meal details'
        ], 500);
    }
}

public function destroy($id)
{
    try {
        $meal = Meals::findOrFail($id);
        $meal->delete();

        return response()->json([
            'success' => true,
            'message' => 'Meal deleted successfully'
        ]);
    } catch (\Exception $e) {
        Log::error("Error deleting meal: " . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Failed to delete meal'
        ], 500);
    }
}

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'date' => 'required|date',
                'meal_items' => 'required|array|min:1',
                'meal_items.*.unit_price' => 'required|numeric|min:0',
                'meal_items.*.quantity' => 'required|integer|min:1',
                'notes' => 'nullable|string'
            ]);

            $totalAmount = collect($validated['meal_items'])->sum(function($item) {
                return $item['unit_price'] * $item['quantity'];
            });

            $meal = Meals::findOrFail($id);
            $meal->update([
                'date' => $validated['date'],
                'meal_items' => $validated['meal_items'],
                'total_amount' => $totalAmount,
                'notes' => $validated['notes'] ?? null
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Meal updated successfully',
                'data' => $meal
            ]);
        } catch (\Exception $e) {
            Log::error("Error updating meal: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Error updating meal',
                'message' => $e->getMessage()
            ], 500);
        }
    }


}
