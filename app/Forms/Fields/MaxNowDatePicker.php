<?php

namespace App\Forms\Fields;

use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;

class MaxNowDatePicker
{
    public static function make(string $name)
    {

        return DatePicker::make('date')
            ->maxDate(fn () => Carbon::now())
            ->default(fn () => Carbon::now())
            ->required();
    }
}
