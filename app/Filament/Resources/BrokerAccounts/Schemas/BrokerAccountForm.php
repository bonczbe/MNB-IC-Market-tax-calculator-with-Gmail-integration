<?php

namespace App\Filament\Resources\BrokerAccounts\Schemas;

use App\Models\Rate;
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
                Textarea::make('broker_name')
                    ->required(),
                Textarea::make('email')
                    ->label('Email address')
                    ->required(),
                Textarea::make('email_subject')
                    ->required(),
                Textarea::make('account_number')
                    ->required(),
                TextInput::make('starting_balance')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('previous_year_minus')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                Textarea::make('filter_number')
                    ->required(),
                Textarea::make('filter_balance')
                    ->required(),
                Select::make('broker_currency')
                    ->options(function () {
                        return Rate::query()->get()->pluck('base_currency', 'base_currency');
                    })
                    ->required(),
            ]);
    }
}
