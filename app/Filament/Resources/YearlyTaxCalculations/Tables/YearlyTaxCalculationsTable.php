<?php

namespace App\Filament\Resources\YearlyTaxCalculations\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class YearlyTaxCalculationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('broker.broker_name')
                    ->label('Broker Name')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('broker.account_number')
                    ->label('Account Number')
                    ->sortable(),
                TextColumn::make('tax_year'),
                TextColumn::make('gross_profit')
                    ->numeric()
                    ->suffix(' '.env('BASE_CURRENCY', 'HUF'))
                    ->sortable(),
                TextColumn::make('loss_carried_forward')
                    ->label('Carried Loss')
                    ->suffix(' '.env('BASE_CURRENCY', 'HUF'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('taxable_income')
                    ->suffix(' '.env('BASE_CURRENCY', 'HUF'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('tax_amount')
                    ->suffix(' '.env('BASE_CURRENCY', 'HUF'))
                    ->color('warning')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('unused_loss')
                    ->color(fn ($state) => $state > 0 ? 'danger' : 'success')
                    ->suffix(' '.env('BASE_CURRENCY', 'HUF'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
            ]);
    }
}
