<?php

namespace App\Filament\TaxCalculator\Resources\DailyStatuses\Pages;

use App\Filament\TaxCalculator\Resources\DailyStatuses\DailyStatusResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDailyStatus extends CreateRecord
{
    protected static string $resource = DailyStatusResource::class;
}
