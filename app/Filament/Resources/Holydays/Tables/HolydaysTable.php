<?php

namespace App\Filament\Resources\Holydays\Tables;

use Carbon\Carbon;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Support\Colors\Color;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class HolydaysTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('date')
                    ->searchable()
                    ->date()
                    ->sortable(),
                TextColumn::make('name')
                    ->sortable(),
                TextColumn::make('status')
                    ->color(fn ($state) => ($state == 'early-close') ? Color::Blue : Color::Red)
                    ->badge(),
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
