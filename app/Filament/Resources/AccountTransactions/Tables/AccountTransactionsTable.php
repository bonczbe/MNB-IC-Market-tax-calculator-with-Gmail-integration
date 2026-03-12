<?php

namespace App\Filament\Resources\AccountTransactions\Tables;

use App\Models\AccountTransaction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AccountTransactionsTable
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
                TextColumn::make('date')
                    ->date()
                    ->sortable(),
                TextColumn::make('type')
                    ->color(fn ($state) => match ($state) {
                        'deposit' => 'success',
                        'withdrawal' => 'warning',
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
                SelectFilter::make('date')
                    ->options(function () {
                        return AccountTransaction::query()->get()->pluck('date', 'date');
                    })
                    ->searchable(),
                SelectFilter::make('type')
                    ->options([
                        'deposit' => 'deposit',
                        'withdrawal' => 'withdrawal',
                    ]),
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
