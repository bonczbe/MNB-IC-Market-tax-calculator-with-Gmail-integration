<?php

namespace App\Filament\Resources\BrokerAccounts\Tables;

use App\Models\BrokerAccount;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BrokerAccountsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => auth()->user()->role === 'admin'
                ? $query
                : $query->where('user_id', auth()->id())
            )
            ->columns([
                TextColumn::make('user.id')
                    ->sortable()
                    ->visible(fn () => auth()->user()->role === 'admin'),
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
                Filter::make('only_mine')
                    ->label('Only my accounts')
                    ->query(fn (Builder $query) => $query->where('user_id', auth()->id()))
                    ->default()
                    ->visible(fn () => auth()->user()->role === 'admin'),
                SelectFilter::make('broker_currency')
                    ->options(function () {
                        return BrokerAccount::query()->distinct()->pluck('broker_currency', 'broker_currency');
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
