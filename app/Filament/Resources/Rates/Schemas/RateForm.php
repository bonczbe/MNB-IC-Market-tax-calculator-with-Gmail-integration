<?php

namespace App\Filament\Resources\Rates\Schemas;

use App\Repositories\RateRepository;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Cache;

class RateForm
{
    public static function configure(Schema $schema): Schema
    {
        $rateRepository = app(RateRepository::class);

        return $schema
            ->components([
                Select::make('base_currency')
                    ->options(function () use ($rateRepository) {
                        $rates = Cache::remember('rateBaseCurrency', Carbon::now()->endOfDay()->subMinute(1), function () use ($rateRepository) {
                            return $rateRepository->getAllDistinctedByKeyValue('base_currency');
                        });

                        return $rates->count() == 0 ? [config('tax.base_broker_currency') => config('tax.base_broker_currency')] : $rates;
                    })
                    ->required(),
                TextInput::make('unit')
                    ->required()
                    ->numeric(),
                TextInput::make('rate')
                    ->required()
                    ->numeric(),
                TextInput::make('for_currency')
                    ->readOnly()
                    ->default(config('tax.base_currency'))

                    ->required(),
                DatePicker::make('date')
                    ->maxDate(fn () => Carbon::now())
                    ->required(),
            ]);
    }
}
