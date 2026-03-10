<?php

namespace App\Filament\Resources\BrokerAccounts\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
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
                TextColumn::make('previous_year_minus')
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
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
            ]);
    }
}
