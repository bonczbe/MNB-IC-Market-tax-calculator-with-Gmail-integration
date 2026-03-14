<?php

namespace App\Filament\Resources\Rates\Tables;

use App\Models\Rate;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class RatesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('date')
                    ->date('Y.m.d')
                    ->sortable(),
                TextColumn::make('base_currency')
                    ->sortable(),
                TextColumn::make('unit')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('rate')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('for_currency')
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
                SelectFilter::make('base_currency')
                    ->options(function () {
                        return Rate::query()->distinct()->pluck('base_currency', 'base_currency');
                    })
                    ->default(config('tax.base_broker_currency')),
                SelectFilter::make('date')
                    ->options(function () {
                        return Rate::query()->distinct()->pluck('date', 'date');
                    })
                    ->searchable(),
            ])
            ->recordActions([
                EditAction::make()->visible(fn () => auth()->user()->role === 'admin'),
                DeleteAction::make()->visible(fn () => auth()->user()->role === 'admin'),
            ])
            ->toolbarActions([
            ]);
    }
}
