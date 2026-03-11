<?php

namespace App\Filament\Resources\YearlyTaxCalculations\Schemas;

use App\Models\BrokerAccount;
use Carbon\Carbon;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class YearlyTaxCalculationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('broker_account_id')
                    ->required()
                    ->options(function () {
                        return BrokerAccount::query()
                            ->get()
                            ->mapWithKeys(fn ($broker) => [
                                $broker->id => $broker->broker_name.' ('.$broker->account_number.')',
                            ])
                            ->toArray();
                    })
                    ->searchable(),
                TextInput::make('tax_year')
                    ->default(fn () => Carbon::now()->subYear()->format('Y'))
                    ->required(),
                TextInput::make('gross_profit')
                    ->required()
                    ->numeric(),
                TextInput::make('loss_carried_forward')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('taxable_income')
                    ->required()
                    ->numeric(),
                TextInput::make('tax_amount')
                    ->required()
                    ->numeric(),
                TextInput::make('unused_loss')
                    ->required()
                    ->numeric()
                    ->default(0.0),
            ]);
    }
}
