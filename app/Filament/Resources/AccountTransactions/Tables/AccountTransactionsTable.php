<?php

namespace App\Filament\Resources\AccountTransactions\Tables;

use App\Enums\UserRoleEnum;
use App\Repositories\AccountTransactionRepository;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Support\Colors\Color;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AccountTransactionsTable
{
    public static function configure(Table $table): Table
    {
        $accountTransactinRepository = app(AccountTransactionRepository::class);

        return $table
            ->modifyQueryUsing(fn ($query) => auth()->user()->role === UserRoleEnum::ADMIN
    ? $query
    : $query->whereHas('broker', fn ($q) => $q->where('user_id', auth()->id()))
            )
            ->columns([
                TextColumn::make('broker.broker_name')
                    ->label('Broker Name')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('broker.account_number')
                    ->label('Account Number')
                    ->sortable(),
                TextColumn::make('date')
                    ->date()
                    ->sortable(),
                TextColumn::make('type')
                    ->color(fn ($state) => match ($state) {
                        'deposit' => Color::Green,
                        'withdrawal' => Color::Amber,
                        default => null,
                    })
                    ->badge(),
                TextColumn::make('amount')
                    ->numeric()
                    ->suffix(fn ($record) => ' '.$record->broker->broker_currency)
                    ->sortable(),
                TextColumn::make('note')
                    ->wrap(),
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
                Filter::make('only_mine')
                    ->label('Only my accounts')
                    ->query(fn (Builder $query) => $query->whereHas(
                        'broker', fn ($q) => $q->where('user_id', auth()->id())
                    ))
                    ->default()
                    ->visible(fn () => auth()->user()->role === UserRoleEnum::ADMIN),
                SelectFilter::make('type')
                    ->options([
                        'deposit' => 'deposit',
                        'withdrawal' => 'withdrawal',
                    ]),
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
