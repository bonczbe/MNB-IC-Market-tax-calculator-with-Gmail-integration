<?php

namespace App\Filament\Resources\Holydays\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class HolydayForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('date')
                    ->required(),
                TextInput::make('name')
                    ->required(),
                Select::make('status')
                    ->options(['closed' => 'Closed', 'early-close' => 'Early close'])
                    ->required(),
            ]);
    }
}
