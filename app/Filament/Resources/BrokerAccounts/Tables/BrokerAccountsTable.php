<?php

namespace App\Filament\Resources\BrokerAccounts\Tables;

use App\Models\BrokerAccount;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class BrokerAccountsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('broker_name')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('email')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('email_subject')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('account_number')
                    ->sortable(),
                TextColumn::make('starting_balance')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('broker_currency')
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
                SelectFilter::make('broker_currency')
                    ->options(function () {
                        return BrokerAccount::query()->get()->pluck('broker_currency', 'broker_currency');
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
