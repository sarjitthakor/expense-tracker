<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Livewire\Expense\ExpenseList;
use App\Livewire\ExpenseManager;

// 🌐 Public routes
Route::get('/', function () {
    return view('welcome');
});

// 🔐 Auth routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

// 🔒 Protected routes
Route::middleware('auth')->group(function () {
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/expenses', ExpenseList::class)->name('expense.list');
});


Route::get('/expenses-livewire', ExpenseManager::class);