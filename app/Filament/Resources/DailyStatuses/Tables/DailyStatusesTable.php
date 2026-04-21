<?php

namespace App\Filament\Resources\DailyStatuses\Tables;

use App\Enums\AccountTransactionTypeEnum;
use App\Enums\UserRoleEnum;
use App\Repositories\DailyStatusRepository;
use Carbon\Carbon;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
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
        $dailyStatusRepository = app(DailyStatusRepository::class);

        return $table
            ->modifyQueryUsing(fn ($query) => auth()->user()->role === UserRoleEnum::ADMIN
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
                    ->sortable(),
                TextColumn::make('broker.account_number')
                    ->label('Account Number')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('profit')
                    ->getStateUsing(function ($record) use ($dailyStatusRepository) {
                        $depositAndWithdrawSum = 0;

                        $prevBalance = Cache::remember(
                            'DailyStatus'.$record->broker->user->id.'$'.$record->date.'$'.$record->broker->id,
                            86400,
                            fn () => $dailyStatusRepository
                                ->firstSmallerDatedStatus($record->broker->id, Carbon::parse($record->date))
                        );

                        $transactions = $record->broker->accountTransactions->filter(fn ($act) => $act->date == $record->date);

                        foreach ($transactions as $transaction) {
                            $value = $transaction->amount;
                            if ($transaction->type == AccountTransactionTypeEnum::WITHDRAWAL) {
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
            ->defaultSort('date', 'desc')
            ->filters([
                Filter::make('only_mine')
                    ->label('Only my accounts')
                    ->query(fn (Builder $query) => $query->whereHas(
                        'broker', fn ($q) => $q->where('user_id', auth()->id())
                    ))
                    ->default()
                    ->visible(fn () => auth()->user()->role === UserRoleEnum::ADMIN),
                SelectFilter::make('broker')
                    ->label('Broker')
                    ->relationship(
                        'broker',
                        'broker_name',
                        fn ($query) => auth()->user()->role === UserRoleEnum::ADMIN
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
                        fn ($query) => auth()->user()->role === UserRoleEnum::ADMIN
                            ? $query
                            : $query->where('user_id', auth()->id())
                    )
                    ->searchable()
                    ->preload(),

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
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
            ]);
    }
}
