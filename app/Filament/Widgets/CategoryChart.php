<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Expense;

class CategoryChart extends ChartWidget
{
    protected ?string $heading = 'Category Chart';

    protected function getData(): array
    {
        $data = Expense::with('category')
            ->get()
            ->groupBy('category.name')
            ->map(fn ($items) => $items->sum('amount'));

        return [
            'datasets' => [
                [
                    'data' => $data->values(),
                ],
            ],
            'labels' => $data->keys(),
        ];
    }


    protected function getType(): string
    {
        return 'pie';
    }
}
