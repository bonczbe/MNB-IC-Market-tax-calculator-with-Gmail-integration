<?php

namespace App\Filament\Resources\ForexEvents\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ForexEventForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                Select::make('country')
                    ->required()
                    ->options([
                        'US' => 'United States',
                        'DE' => 'Germany',
                        'FR' => 'France',
                        'IT' => 'Italy',
                        'ES' => 'Spain',
                        'UK' => 'United Kingdom',
                    ]),
                DateTimePicker::make('date')
                    ->required(),
                TextInput::make('importance')
                    ->required()
                    ->minValue(0)
                    ->maxValue(3)
                    ->numeric(),
                TextInput::make('previouse')
                    ->default(null),
                TextInput::make('forecast')
                    ->default(null),
            ]);
    }
}
