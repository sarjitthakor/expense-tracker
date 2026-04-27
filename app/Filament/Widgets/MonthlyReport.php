<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use App\Models\Expense;
use App\Models\Budget;

class MonthlyReport extends BaseWidget
{
    protected function getCards(): array
    {
        $month = now()->month;
        $year = now()->year;

        $totalSpent = Expense::where('user_id', auth()->id())
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->sum('amount');

        $totalBudget = Budget::where('user_id', auth()->id())
            ->where('month', $month)
            ->where('year', $year)
            ->sum('monthly_limit');

        $remaining = $totalBudget - $totalSpent;

        return [
            Card::make('Total Spent', '₹' . number_format($totalSpent))
                ->color('danger'),

            Card::make('Total Budget', '₹' . number_format($totalBudget))
                ->color('primary'),

            Card::make('Remaining', '₹' . number_format($remaining))
                ->color($remaining < 0 ? 'danger' : 'success'),
        ];
    }
}
