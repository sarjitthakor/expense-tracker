<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Expense;
use Carbon\Carbon;

class ExpenseChart extends ChartWidget
{
    // protected ?string $heading = 'Expense Chart';
    // protected static ?string $heading = 'Monthly Expense Trend';
    protected ?string $heading = 'Monthly Expense Trend';

    protected function getData(): array
    {

         $data = [];
        $labels = [];

        // Last 29 days data
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);

            $labels[] = $date->format('d M');

            $data[] = Expense::whereDate('date', $date)
                ->sum('amount');
        }

        return [
            'datasets' => [
                [
                    'label' => 'Expenses',
                    'data' => $data,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
