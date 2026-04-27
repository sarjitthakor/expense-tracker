<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Expense;
use Carbon\Carbon;

class ExpenseStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
                // 💰 Total expenses
                Stat::make('Total Expenses', '₹ ' . Expense::sum('amount')),

                // 📅 This month expenses
                Stat::make(
                    'This Month',
                    '₹ ' . Expense::whereMonth('date', Carbon::now()->month)->sum('amount')
                ),

                // 📆 Today expenses
                Stat::make(
                    'Today',
                    '₹ ' . Expense::whereDate('date', Carbon::today())->sum('amount')
                ),

                // ⚠️ Budget alert (example logic)
                Stat::make(
                    'Budget Status',
                    Expense::sum('amount') > 50000 ? 'Over Budget ⚠️' : 'Within Budget ✅'
                ),
        ];
    }
}
