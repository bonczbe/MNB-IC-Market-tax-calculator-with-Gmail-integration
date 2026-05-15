<?php

namespace App\Filament\Resources\DailyStatuses\Schemas;

use App\Models\BrokerAccount;
use App\Repositories\BrokerAccountRepository;
use App\Repositories\RateRepository;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Cache;

class DailyStatusForm
{
    public static function configure(Schema $schema): Schema
    {
        $rateRepository = app(RateRepository::class);
        $brokerRepository = app(BrokerAccountRepository::class);

        return $schema
            ->components([
                DatePicker::make('date')
                    ->maxDate(fn () => Carbon::now())
                    ->required(),
                TextInput::make('balance')
                    ->required()
                    ->numeric(),
                Select::make('broker_account_id')
                    ->live()
                    ->required()
                    ->afterStateUpdated(function ($state, Set $set) use ($brokerRepository) {
                        $broker = $brokerRepository->findById($state);
                        $set('currency', $broker?->broker_currency ?? null);
                    })
                    ->options(function () {
                        return BrokerAccount::query()
                            ->get()
                            ->mapWithKeys(fn ($broker) => [
                                $broker->id => $broker->broker_name.' ('.$broker->account_number.')',
                            ])
                            ->toArray();
                    })
                    ->searchable(),
                Select::make('currency')
                    ->live()
                    ->options(function () use ($rateRepository) {

                        return Cache::remember('rateBaseCurrency', Carbon::now()->endOfDay()->subMinute(1), function () use ($rateRepository) {
                            return $rateRepository->getAllDistinctedByKeyValue('base_currency');
                        });
                    })
                    ->required(),
            ]);
    }
}
