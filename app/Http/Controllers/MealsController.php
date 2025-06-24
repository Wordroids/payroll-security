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
        $currentMonth = $request->input('month');
        $employeeId = $request->input('employee_id');
        $showAll = $request->input('show_all', false);

        // Employees with meals
        $query = Employee::with(['meals' => function($query) use ($currentMonth, $showAll) {
            if (!$showAll && $currentMonth) {
                $query->whereMonth('date', '=', substr($currentMonth, 5, 2))
                      ->whereYear('date', '=', substr($currentMonth, 0, 4));
            }
        }])->whereHas('meals');

        // Filter by employee if specified
        if ($employeeId) {
            $query->where('id', $employeeId);
        }

        // If filtering by month
        if (!$showAll && $currentMonth) {
            $query->whereHas('meals', function($query) use ($currentMonth) {
                $query->whereMonth('date', '=', substr($currentMonth, 5, 2))
                      ->whereYear('date', '=', substr($currentMonth, 0, 4));
            });
        }

        $employees = $query->orderBy('name')->paginate(10);
        $allEmployees = Employee::orderBy('name')->get();

        // Calculate total meal costs
        $totalQuery = Meals::query();
        if (!$showAll && $currentMonth) {
            $totalQuery->whereMonth('date', '=', substr($currentMonth, 5, 2))
                        ->whereYear('date', '=', substr($currentMonth, 0, 4));
        }
        $totalMealCosts = $totalQuery->sum('total_amount');

        return view('pages.meals.index', [
            'employees' => $employees,
            'allEmployees' => $allEmployees,
            'currentMonth' => $currentMonth,
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
            $month = $request->input('month');
            $showAll = !$request->has('month');

            $employee = Employee::with(['meals' => function($query) use ($month, $showAll) {
                if (!$showAll && $month) {
                    $query->whereYear('date', '=', substr($month, 0, 4))
                          ->whereMonth('date', '=', substr($month, 5, 2));
                }
                $query->orderBy('date');
            }])->findOrFail($employeeId);

            return view('pages.meals.edit', [
                'employee' => $employee,
                'meals' => $employee->meals,
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
