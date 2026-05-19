<?php

namespace App\Filament\TaxCalculator\Resources\EmailExtracts\Pages;

use App\Filament\TaxCalculator\Resources\EmailExtracts\EmailExtractResource;
use Filament\Resources\Pages\ListRecords;

class ListEmailExtracts extends ListRecords
{
    protected static string $resource = EmailExtractResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
