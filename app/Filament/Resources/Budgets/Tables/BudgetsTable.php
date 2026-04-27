<?php

namespace App\Filament\Resources\Budgets\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;

class BudgetsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                // ✅ Category Name (relation)
                TextColumn::make('category.name')
                    ->label('Category')
                    ->searchable(),

                // ✅ Budget Amount
                TextColumn::make('monthly_limit')
                    ->label('Budget')
                    ->money('INR')
                    ->sortable(),

                // ✅ Month (better UI)
                BadgeColumn::make('month')
                    ->formatStateUsing(fn ($state) => \Carbon\Carbon::create()->month($state)->format('F'))
                    ->colors([
                        'primary',
                    ]),

                // ✅ Year
                TextColumn::make('year')
                    ->sortable(),

                // ✅ Created date (optional)
                TextColumn::make('created_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])

            ->filters([

                // 📂 Category filter
                SelectFilter::make('category_id')
                    ->relationship(
                        'category',
                        'name',
                        fn ($query) => $query->where('user_id', auth()->id())
                    ),

                // 📅 Month filter
                SelectFilter::make('month')
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
                    ]),

                // 📅 Year filter
                SelectFilter::make('year')
                    ->options(
                        collect(range(now()->year - 5, now()->year + 1))
                            ->mapWithKeys(fn ($year) => [$year => $year])
                            ->toArray()
                    ),
            ])

            ->recordActions([
                EditAction::make(),
            ])

            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);

    }
}
