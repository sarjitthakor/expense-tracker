<?php

namespace App\Filament\Resources\Expenses\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;

use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;

use Filament\Forms\Components\DatePicker;

use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;


use Filament\Tables\Table;

class ExpensesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // ❌ REMOVE user column (not needed)

                TextColumn::make('category.name')
                    ->label('Category')
                    ->searchable(),

                TextColumn::make('amount')
                    ->money(fn ($record) => $record->currency ?? 'INR')
                    ->sortable(),

                TextColumn::make('expense_date')
                    ->date()
                    ->sortable(),

                IconColumn::make('is_recurring')
                    ->boolean(),

                BadgeColumn::make('recurring_frequency')
                    ->colors([
                        'primary' => 'daily',
                        'warning' => 'weekly',
                        'success' => 'monthly',
                    ]),

                TextColumn::make('currency'),
            ])

            ->filters([

                // 📅 Date Range
                Filter::make('date_range')
                    ->form([
                        DatePicker::make('from'),
                        DatePicker::make('to'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from'],
                                fn ($q) => $q->whereDate('expense_date', '>=', $data['from']))
                            ->when($data['to'],
                                fn ($q) => $q->whereDate('expense_date', '<=', $data['to']));
                    }),

                // 📂 Category filter (only user categories)
                SelectFilter::make('category_id')
                    ->relationship(
                        'category',
                        'name',
                        fn ($query) => $query->where('user_id', auth()->id())
                    ),

                // 🔁 Recurring
                TernaryFilter::make('is_recurring'),

            ])

            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])

            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
