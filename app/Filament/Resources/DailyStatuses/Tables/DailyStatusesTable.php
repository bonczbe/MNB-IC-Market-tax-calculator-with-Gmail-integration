<?php

namespace App\Filament\Resources\DailyStatuses\Tables;

use App\Models\DailyStatus;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Support\Colors\Color;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

class DailyStatusesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => auth()->user()->role === 'admin'
    ? $query
    : $query->whereHas('broker', fn ($q) => $q->where('user_id', auth()->id()))
            )
            ->columns([
                TextColumn::make('date')
                    ->date('Y.m.d')
                    ->sortable(),
                TextColumn::make('balance')
                    ->numeric()
                    ->suffix(fn ($record) => ' '.$record->broker->broker_currency)
                    ->sortable(),
                TextColumn::make('broker.broker_name')
                    ->label('Broker Name')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('broker.account_number')
                    ->label('Account Number')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('profit')
                    ->getStateUsing(function ($record) {
                        $depositAndWithdrawSum = 0;

                        $prevBalance = Cache::remember('DailyStatus'.$record->broker->user->id.'$'.$record->date.$record->broker->broker_name.$record->broker->account_number, 86400, fn () => DailyStatus::query()
                            ->where('date', '<', $record->date)
                            ->orderByDesc('date')
                            ->first());

                        $transactions = $record->broker->accountTransactions->filter(fn ($act) => $act->date == $record->date);

                        foreach ($transactions as $transaction) {
                            $value = $transaction->amount;
                            if ($transaction->type == 'withdrawal') {
                                $value *= -1;
                            }
                            $depositAndWithdrawSum += $value;
                        }

                        if ($prevBalance == null) {
                            return $record->balance - $record->broker->starting_balance - $depositAndWithdrawSum;
                        }

                        return $record->balance - $prevBalance->balance - $depositAndWithdrawSum;
                    })
                    ->suffix(fn ($record) => ' '.$record->broker->broker_currency)
                    ->color(fn ($state) => ((float) $state > 0) ? Color::Green : Color::Red)
                    ->numeric()
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('only_mine')
                    ->label('Only my accounts')
                    ->query(fn (Builder $query) => $query->whereHas(
                        'broker', fn ($q) => $q->where('user_id', auth()->id())
                    ))
                    ->default()
                    ->visible(fn () => auth()->user()->role === 'admin'),
                SelectFilter::make('date')
                    ->options(function () {
                        return DailyStatus::query()->distinct()->pluck('date', 'date');
                    })
                    ->searchable(),
                SelectFilter::make('broker')
                    ->label('Broker')
                    ->relationship(
                        'broker',
                        'broker_name',
                        fn ($query) => auth()->user()->role === 'admin'
                            ? $query
                            : $query->where('user_id', auth()->id())
                    )
                    ->searchable()
                    ->preload(),
                SelectFilter::make('account_number')
                    ->label('Account Number')
                    ->relationship(
                        'broker',
                        'account_number',
                        fn ($query) => auth()->user()->role === 'admin'
                            ? $query
                            : $query->where('user_id', auth()->id())
                    )
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
