<?php

namespace App\Filament\Resources\EmailExtracts\Pages;

use App\Filament\Resources\EmailExtracts\EmailExtractResource;
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
