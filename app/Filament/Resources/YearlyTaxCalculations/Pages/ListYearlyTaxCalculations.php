<?php

namespace App\Filament\Resources\YearlyTaxCalculations\Pages;

use App\Filament\Resources\YearlyTaxCalculations\YearlyTaxCalculationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListYearlyTaxCalculations extends ListRecords
{
    protected static string $resource = YearlyTaxCalculationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
