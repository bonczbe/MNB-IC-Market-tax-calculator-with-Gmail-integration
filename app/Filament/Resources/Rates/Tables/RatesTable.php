<?php

namespace App\Filament\Resources\Rates\Tables;

use App\Repositories\RateRepository;
use Carbon\Carbon;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
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
                        return Cache::remember('rateBaseCurrency', Carbon::now()->endOfDay()->subMinute(1), function () use ($rateRepository) {
                            return $rateRepository->getAllDistinctedByKeyValue('base_currency');
                        });
                    })
                    ->default(config('tax.base_broker_currency')),
                Filter::make('date_range')
                    ->schema([
                        DatePicker::make('from')
                            ->label('From date'),
                        DatePicker::make('to')
                            ->label('To date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $from = $data['from'] ?? null;
                        $to = $data['to'] ?? null;

                        return $query
                            ->when($from, fn (Builder $q, $date): Builder => $q->whereDate('date', '>=', $date)
                            )
                            ->when($to, fn (Builder $q, $date): Builder => $q->whereDate('date', '<=', $date)
                            );
                    }),
            ])
            ->recordActions([
                EditAction::make()->visible(fn () => auth()->user()->role === 'admin'),
                DeleteAction::make()->visible(fn () => auth()->user()->role === 'admin'),
            ])
            ->toolbarActions([
            ]);
    }
}
