<?php

namespace App\Filament\Resources\YearlyTaxCalculations\Tables;

use App\Models\YearlyTaxCalculation;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
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
                    ->suffix(' '.config('tax.base_currency'))
                    ->sortable(),
                TextColumn::make('loss_carried_forward')
                    ->label('Carried Loss')
                    ->suffix(' '.config('tax.base_currency'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('taxable_income')
                    ->suffix(' '.config('tax.base_currency'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('tax_amount')
                    ->suffix(' '.config('tax.base_currency'))
                    ->color('warning')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('unused_loss')
                    ->color(fn ($state) => $state > 0 ? 'danger' : 'success')
                    ->suffix(' '.config('tax.base_currency'))
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
                SelectFilter::make('tax_year')
                    ->options(function () {
                        return YearlyTaxCalculation::query()->get()->pluck('tax_year', 'tax_year');
                    })
                    ->searchable(),
                SelectFilter::make('broker')
                    ->label('Broker')
                    ->relationship('broker', 'broker_name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('account_number')
                    ->label('Account Number')
                    ->relationship('broker', 'account_number')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
            ]);
    }
}
