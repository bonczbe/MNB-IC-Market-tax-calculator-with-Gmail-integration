<?php

namespace App\Filament\TaxCalculator\Resources\EmailExtracts\Pages;

use App\Filament\TaxCalculator\Resources\EmailExtracts\EmailExtractResource;
use Filament\Resources\Pages\ViewRecord;

class ViewEmailExtract extends ViewRecord
{
    protected static string $resource = EmailExtractResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
