<?php

namespace App\Filament\Resources\EmailExtracts\Tables;

use App\Repositories\EmailExtractRepository;
use Carbon\Carbon;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

class EmailExtractsTable
{
    public static function configure(Table $table): Table
    {
        $emailExtRepository = app(EmailExtractRepository::class);

        return $table
            ->modifyQueryUsing(fn ($query) => auth()->user()->role === 'admin'
    ? $query
    : $query->whereHas('broker', fn ($q) => $q->where('user_id', auth()->id()))
            )
            ->columns([
                TextColumn::make('broker_account_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('date')
                    ->date()
                    ->sortable(),
                TextColumn::make('broker.broker_name')
                    ->label('Broker Name')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('broker.account_number')
                    ->label('Account Number')
                    ->sortable(),
                TextColumn::make('content')
                    ->limit(50)
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
                    ->query(fn (Builder $query) => $query->whereHas(
                        'broker', fn ($q) => $q->where('user_id', auth()->id())
                    ))
                    ->default()
                    ->visible(fn () => auth()->user()->role === 'admin'),
                SelectFilter::make('date')
                    ->options(function () use ($emailExtRepository) {
                        return Cache::remember('emailExtractDates', Carbon::now()->endOfDay()->subMinute(1), function () use ($emailExtRepository) {
                            return $emailExtRepository->getAllDistinctedByKeyValue('date');
                        });
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
                ViewAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
            ]);
    }
}
