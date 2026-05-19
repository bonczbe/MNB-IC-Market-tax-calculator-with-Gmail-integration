<?php

namespace App\Filament\TaxCalculator\Resources\Holydays\Pages;

use App\Filament\TaxCalculator\Resources\Holydays\HolydayResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditHolyday extends EditRecord
{
    protected static string $resource = HolydayResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
