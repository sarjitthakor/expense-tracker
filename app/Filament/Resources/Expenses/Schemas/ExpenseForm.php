<?php

namespace App\Filament\Resources\Expenses\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ExpenseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                // ✅ Category (only logged-in user's categories)
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
                    ->exists('categories', 'id'),

                // ✅ Amount
                TextInput::make('amount')
                    ->label('Amount (₹)')
                    ->numeric()
                    ->required()
                    ->minValue(1)
                    ->prefix('₹')
                    ->rules(['required', 'numeric', 'min:1']),

                // ✅ Expense Date
                DatePicker::make('expense_date')
                    ->label('Expense Date')
                    ->required()
                    ->rules(['required', 'date']),

                // ✅ Note / Description
                Textarea::make('note')
                    ->label('Note')
                    ->maxLength(500)
                    ->nullable()
                    ->columnSpanFull(),

                // ✅ Recurring Toggle
                Toggle::make('is_recurring')
                    ->label('Recurring Expense')
                    ->default(false),

                // ✅ Recurring Frequency (only if recurring)
                Select::make('recurring_frequency')
                    ->label('Frequency')
                    ->options([
                        'daily' => 'Daily',
                        'weekly' => 'Weekly',
                        'monthly' => 'Monthly',
                    ])
                    ->visible(fn ($get) => $get('is_recurring'))
                    ->required(fn ($get) => $get('is_recurring'))
                    ->rules([
                        fn ($get) => $get('is_recurring') ? 'required' : 'nullable'
                    ]),

                // ✅ Currency
                Select::make('currency')
                    ->label('Currency')
                    ->options([
                        'INR' => '₹ INR',
                        'USD' => '$ USD',
                    ])
                    ->default('INR')
                    ->required()
                    ->rules(['required', 'in:INR,USD']),
            ]);
    }
}