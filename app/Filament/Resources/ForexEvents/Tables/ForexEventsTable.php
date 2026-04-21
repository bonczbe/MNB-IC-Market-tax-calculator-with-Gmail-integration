<?php

namespace App\Filament\Resources\ForexEvents\Tables;

use App\Enums\CountryEnum;
use Carbon\Carbon;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
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
                    ->searchable()
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('name')
                    ->sortable(),
                TextColumn::make('country')
                    ->formatStateUsing(fn ($state) => $state->label())
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
