<?php

namespace App\Filament\TaxCalculator\Resources\YearlyTaxCalculations\Pages;

use App\Filament\TaxCalculator\Resources\YearlyTaxCalculations\YearlyTaxCalculationResource;
use Filament\Resources\Pages\CreateRecord;

class CreateYearlyTaxCalculation extends CreateRecord
{
    protected static string $resource = YearlyTaxCalculationResource::class;
}
