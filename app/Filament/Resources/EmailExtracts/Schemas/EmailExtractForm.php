<?php

namespace App\Filament\Resources\EmailExtracts\Schemas;

use App\Forms\Fields\MaxNowDatePicker;
use App\Models\BrokerAccount;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class EmailExtractForm
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
                    ->disabled()
                    ->searchable(),
                MaxNowDatePicker::make('date')
                    ->disabled(),
                Textarea::make('content')
                    ->required()
                    ->disabled()
                    ->rows(15)
                    ->columnSpanFull(),
            ]);
    }
}
