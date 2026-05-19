<?php

namespace App\Filament\TaxCalculator\Resources\Holydays\Pages;

use App\Filament\TaxCalculator\Resources\Holydays\HolydayResource;
use Filament\Resources\Pages\CreateRecord;

class CreateHolyday extends CreateRecord
{
    protected static string $resource = HolydayResource::class;
}
