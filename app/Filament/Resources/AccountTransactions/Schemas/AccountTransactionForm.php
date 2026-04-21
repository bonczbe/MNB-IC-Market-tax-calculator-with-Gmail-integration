<?php

namespace App\Filament\Resources\AccountTransactions\Schemas;

use App\Enums\AccountTransactionTypeEnum;
use App\Models\BrokerAccount;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AccountTransactionForm
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
                DatePicker::make('date')
                    ->maxDate(fn () => Carbon::now())
                    ->default(fn () => Carbon::now())
                    ->required(),
                Select::make('type')
                    ->options(AccountTransactionTypeEnum::options())
                    ->required(),
                TextInput::make('amount')
                    ->required()
                    ->numeric(),
                Textarea::make('note')
                    ->default(null)
                    ->columnSpanFull(),
            ]);
    }
}
