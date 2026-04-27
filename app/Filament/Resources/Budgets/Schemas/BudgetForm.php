<?php

namespace App\Filament\Resources\Budgets\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rule;

class BudgetForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                // ✅ Category (only current user's categories)
                Select::make('category_id')
                    ->label('Category')
                    ->relationship(
                        name: 'category',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn ($query) =>
                            $query->where('user_id', auth()->id())
                    )
                    ->searchable()
                    ->preload()
                    ->required()
                    ->exists('categories', 'id')

                    // 🔥 Prevent duplicate budget
                    ->rule(function ($get, $record) {
                        return Rule::unique('budgets')
                            ->ignore($record?->id) // ✅ important for edit
                            ->where(fn ($query) =>
                                $query->where('user_id', auth()->id())
                                      ->where('category_id', $get('category_id'))
                                      ->where('month', $get('month'))
                                      ->where('year', $get('year'))
                            );
                    }),

                // ✅ Monthly Limit
                TextInput::make('monthly_limit')
                    ->label('Monthly Budget (₹)')
                    ->numeric()
                    ->required()
                    ->minValue(1)
                    ->prefix('₹')
                    ->rules(['required', 'numeric', 'min:1']),

                // ✅ Month
                Select::make('month')
                    ->label('Month')
                    ->options([
                        1 => 'January',
                        2 => 'February',
                        3 => 'March',
                        4 => 'April',
                        5 => 'May',
                        6 => 'June',
                        7 => 'July',
                        8 => 'August',
                        9 => 'September',
                        10 => 'October',
                        11 => 'November',
                        12 => 'December',
                    ])
                    ->required()
                    ->rules(['required', 'integer', 'between:1,12']),

                // ✅ Year
                TextInput::make('year')
                    ->label('Year')
                    ->numeric()
                    ->default(now()->year)
                    ->required()
                    ->rules(['required', 'numeric', 'min:2020']),
            ]);
    }
}
