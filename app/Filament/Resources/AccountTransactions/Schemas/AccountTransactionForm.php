<?php

namespace App\Filament\Resources\AccountTransactions\Schemas;

use App\Enums\AccountTransactionTypeEnum;
use App\Forms\Fields\MaxNowDatePicker;
use App\Models\BrokerAccount;
use App\Repositories\BrokerAccountRepository;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class AccountTransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        $brokerRepository = app(BrokerAccountRepository::class);

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
                    ->live()
                    ->searchable(),
                MaxNowDatePicker::make('date'),
                Select::make('type')
                    ->options(AccountTransactionTypeEnum::options())
                    ->required(),
                TextInput::make('amount')
                    ->live()
                    ->suffix(function (Get $get) use ($brokerRepository) {
                        $broker = $brokerRepository->findById($get('broker_account_id'));
                        if ($broker) {
                            return $broker->broker_currency;
                        }
                    })
                    ->required()
                    ->numeric(),
                Textarea::make('note')
                    ->default(null)
                    ->columnSpanFull(),
            ]);
    }
}
