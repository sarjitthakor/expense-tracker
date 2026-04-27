<?php

namespace App\Http\Controllers\Api;

use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class ExpenseController extends Controller
{
    /**
     * Get all expenses for authenticated user
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Expense::where('user_id', auth()->id());

            // 🔍 Filter by category
            if ($request->has('category_id')) {
                $query->where('category_id', $request->category_id);
            }

            // 📅 Filter by date range
            if ($request->has('from_date')) {
                $query->whereDate('date', '>=', $request->from_date);
            }

            if ($request->has('to_date')) {
                $query->whereDate('date', '<=', $request->to_date);
            }

            // 🔎 Search
            if ($request->has('search')) {
                $query->where('description', 'like', '%' . $request->search . '%');
            }

            $expenses = $query->with('category')->latest()->paginate(20);

            return response()->json([
                'success' => true,
                'data' => $expenses,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get single expense
     */
    public function show($id): JsonResponse
    {
        try {
            $expense = Expense::where('user_id', auth()->id())->with('category')->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $expense,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Expense not found',
            ], 404);
        }
    }

    /**
     * Create new expense
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'category_id' => 'required|exists:categories,id',
                'amount' => 'required|numeric|min:0.01',
                'description' => 'nullable|string|max:500',
                'date' => 'required|date',
                'is_recurring' => 'boolean',
                'recurring_frequency' => 'nullable|in:daily,weekly,monthly,yearly',
                'currency' => 'nullable|string|max:10',
            ]);

            $validated['user_id'] = auth()->id();
            $expense = Expense::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Expense created successfully',
                'data' => $expense->load('category'),
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update expense
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $expense = Expense::where('user_id', auth()->id())->findOrFail($id);

            $validated = $request->validate([
                'category_id' => 'sometimes|exists:categories,id',
                'amount' => 'sometimes|numeric|min:0.01',
                'description' => 'nullable|string|max:500',
                'date' => 'sometimes|date',
                'is_recurring' => 'boolean',
                'recurring_frequency' => 'nullable|in:daily,weekly,monthly,yearly',
            ]);

            $expense->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Expense updated successfully',
                'data' => $expense->load('category'),
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Expense not found',
            ], 404);
        }
    }

    /**
     * Delete expense
     */
    public function destroy($id): JsonResponse
    {
        try {
            $expense = Expense::where('user_id', auth()->id())->findOrFail($id);
            $expense->delete();

            return response()->json([
                'success' => true,
                'message' => 'Expense deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Expense not found',
            ], 404);
        }
    }

    /**
     * Get summary statistics
     */
    public function summary(Request $request): JsonResponse
    {
        try {
            $query = Expense::where('user_id', auth()->id());

            // 📅 This month
            if ($request->has('month')) {
                $query->whereMonth('date', $request->month);
            }

            if ($request->has('year')) {
                $query->whereYear('date', $request->year);
            }

            $totalExpenses = $query->sum('amount');
            $expenseCount = $query->count();
            $averageExpense = $expenseCount > 0 ? $totalExpenses / $expenseCount : 0;

            return response()->json([
                'success' => true,
                'data' => [
                    'total_expenses' => $totalExpenses,
                    'expense_count' => $expenseCount,
                    'average_expense' => round($averageExpense, 2),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
