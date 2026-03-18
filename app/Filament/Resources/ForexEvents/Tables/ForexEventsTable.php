<?php

namespace App\Filament\Resources\ForexEvents\Tables;

use Carbon\Carbon;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ForexEventsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('date')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('name')
                    ->sortable(),
                TextColumn::make('importance')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('previouse')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('forecast')
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
            ->defaultSort('date', 'asc')
            ->filters([
                Filter::make('only_future')
                    ->label('Only future events')
                    ->query(fn (Builder $query) => $query->where('date', '>=', Carbon::now()))
                    ->default(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
            ]);
    }
}
