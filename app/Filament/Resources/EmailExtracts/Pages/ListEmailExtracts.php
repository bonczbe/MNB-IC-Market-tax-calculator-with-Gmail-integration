<?php

namespace App\Filament\Resources\EmailExtracts\Pages;

use App\Filament\Resources\EmailExtracts\EmailExtractResource;
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
