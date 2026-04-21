<?php

namespace App\Filament\Resources\BrokerAccounts\Schemas;

use App\Enums\UserRoleEnum;
use App\Models\User;
use App\Repositories\RateRepository;
use Carbon\Carbon;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Cache;

class BrokerAccountForm
{
    public static function configure(Schema $schema): Schema
    {
        $rateRepository = app(RateRepository::class);

        return $schema
            ->columns(2)
            ->components([
                Select::make('user_id')
                    ->label('Owner')
                    ->options(User::pluck('name', 'id'))
                    ->searchable()
                    ->required()
                    ->default(auth()->id())
                    ->visible(fn () => auth()->user()->role == UserRoleEnum::ADMIN),
                Hidden::make('user_id')
                    ->default(auth()->id())
                    ->visible(fn () => auth()->user()->role != UserRoleEnum::ADMIN),
                TextInput::make('broker_name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->required(),
                TextInput::make('email_subject')
                    ->required(),
                TextInput::make('account_number')
                    ->required(),
                TextInput::make('starting_balance')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                Textarea::make('filter_number')
                    ->required(),
                Textarea::make('filter_balance')
                    ->required(),
                Textarea::make('filter_date')
                    ->nullable(),
                Select::make('broker_currency')
                    ->options(function () use ($rateRepository) {
                        $rates = Cache::remember('rateBaseCurrency', Carbon::now()->endOfDay()->subMinute(1), function () use ($rateRepository) {
                            return $rateRepository->getAllDistinctedByKeyValue('base_currency');
                        });

                        return $rates->count() == 0 ? [config('tax.base_broker_currency') => config('tax.base_broker_currency')] : $rates;
                    })
                    ->required(),
            ]);
    }
}
