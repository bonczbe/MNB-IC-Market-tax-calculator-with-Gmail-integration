<?php

namespace App\Filament\Resources\DailyStatuses\Schemas;

use App\Models\BrokerAccount;
use App\Models\Rate;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class DailyStatusForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('date')
                    ->required(),
                Select::make('currency')
                    ->options(function () {
                        return Rate::query()->get()->pluck('base_currency', 'base_currency');
                    })
                    ->required(),
                TextInput::make('balance')
                    ->required()
                    ->numeric(),
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
            ]);
    }
}
