<?php

namespace App\Filament\Resources\BrokerAccounts\Schemas;

use App\Models\Rate;
use App\Models\User;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class BrokerAccountForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Select::make('user_id')
                    ->label('Owner')
                    ->options(User::pluck('name', 'id'))
                    ->searchable()
                    ->required()
                    ->default(auth()->id())
                    ->visible(fn () => auth()->user()->role == 'admin'),
                Hidden::make('user_id')
                    ->default(auth()->id())
                    ->visible(fn () => auth()->user()->role != 'admin'),
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
                    ->required(),
                Select::make('broker_currency')
                    ->options(function () {
                        $rates = Rate::query()->distinct()->pluck('base_currency', 'base_currency');

                        return $rates->count() == 0 ? [config('tax.base_broker_currency') => config('tax.base_broker_currency')] : $rates;
                    })
                    ->required(),
            ]);
    }
}
