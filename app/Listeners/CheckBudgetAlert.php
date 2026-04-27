<?php

namespace App\Listeners;

use App\Events\ExpenseCreated;
use App\Models\Budget;
use App\Notifications\BudgetExceededNotification;
use Illuminate\Support\Facades\Notification;

class CheckBudgetAlert
{
    /**
     * Handle the event.
     */
    public function handle(ExpenseCreated $event): void
    {
        $expense = $event->expense;
        $user = $expense->user;
        $month = $expense->date->month;
        $year = $expense->date->year;

        // 🔍 Get budget for this category
        $budget = Budget::where('user_id', $user->id)
            ->where('category_id', $expense->category_id)
            ->where('month', $month)
            ->where('year', $year)
            ->first();

        // If no budget set, skip
        if (!$budget) {
            return;
        }

        // 📊 Calculate total expenses for this category this month
        $totalExpenses = $expense->user->expenses()
            ->where('category_id', $expense->category_id)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->sum('amount');

        // ⚠️ Check if budget exceeded
        $percentageUsed = ($totalExpenses / $budget->monthly_limit) * 100;

        if ($totalExpenses > $budget->monthly_limit) {
            // 🔴 Budget exceeded
            $user->notify(new BudgetExceededNotification(
                $expense->category->name,
                $budget->monthly_limit,
                $totalExpenses,
                $percentageUsed
            ));
        } elseif ($percentageUsed >= 80) {
            // 🟡 Budget warning (80%+)
            $user->notify(new BudgetExceededNotification(
                $expense->category->name,
                $budget->monthly_limit,
                $totalExpenses,
                $percentageUsed,
                warning: true
            ));
        }
    }
}
