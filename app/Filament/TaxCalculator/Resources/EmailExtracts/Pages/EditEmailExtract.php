<?php

namespace App\Filament\TaxCalculator\Resources\EmailExtracts\Pages;

use App\Filament\TaxCalculator\Resources\EmailExtracts\EmailExtractResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditEmailExtract extends EditRecord
{
    protected static string $resource = EmailExtractResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
