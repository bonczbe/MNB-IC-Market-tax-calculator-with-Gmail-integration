<?php

namespace App\Filament\Resources\Rates\Schemas;

use App\Models\Rate;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class RateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('base_currency')
                    ->options(function () {
                        $rates = Rate::query()->get()->pluck('base_currency', 'base_currency');

                        return $rates->count() == 0 ? ['EUR' => 'EUR'] : $rates;
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
