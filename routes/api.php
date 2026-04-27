<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ExpenseController;
use App\Http\Controllers\Api\BudgetController;
use App\Http\Controllers\Api\CategoryController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// 🔐 Authenticated API routes
Route::middleware('auth:api')->group(function () {
    // 💰 Expense endpoints
    Route::apiResource('expenses', ExpenseController::class);
    Route::get('expenses/summary', [ExpenseController::class, 'summary']);

    // 💵 Budget endpoints
    Route::apiResource('budgets', BudgetController::class);
    Route::get('budgets/category/{categoryId}', [BudgetController::class, 'byCategory']);

    // 📂 Category endpoints
    Route::apiResource('categories', CategoryController::class);
});

// 👤 User profile endpoint
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return response()->json([
        'success' => true,
        'data' => $request->user(),
    ]);
});
