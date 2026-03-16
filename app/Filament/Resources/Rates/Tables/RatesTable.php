<?php

namespace App\Filament\Resources\Rates\Tables;

use App\Repositories\RateRepository;
use Carbon\Carbon;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Cache;

class RatesTable
{
    public static function configure(Table $table): Table
    {
        $rateRepository = app(RateRepository::class);

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
            ->defaultSort('date', 'desc')
            ->filters([
                SelectFilter::make('base_currency')
                    ->options(function () use ($rateRepository) {
                        return Cache::remember('rateBaseCurrency', Carbon::now()->endOfDay()->subMinute(5), function () use ($rateRepository) {
                            return $rateRepository->getAllDistinctedByKeyValue('base_currency');
                        });
                    })
                    ->default(config('tax.base_broker_currency')),
                SelectFilter::make('date')
                    ->options(function () use ($rateRepository) {
                        return Cache::remember('rateDate', Carbon::now()->endOfDay()->subMinute(5), function () use ($rateRepository) {
                            return $rateRepository->getAllDistinctedByKeyValue('date');
                        });
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
