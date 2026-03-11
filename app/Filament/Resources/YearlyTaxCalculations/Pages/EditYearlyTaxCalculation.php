<?php

namespace App\Filament\Resources\YearlyTaxCalculations\Pages;

use App\Filament\Resources\YearlyTaxCalculations\YearlyTaxCalculationResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditYearlyTaxCalculation extends EditRecord
{
    protected static string $resource = YearlyTaxCalculationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
