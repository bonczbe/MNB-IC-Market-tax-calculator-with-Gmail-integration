<?php

namespace App\Filament\TaxCalculator\Resources\ForexEvents\Pages;

use App\Filament\TaxCalculator\Resources\ForexEvents\ForexEventResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditForexEvent extends EditRecord
{
    protected static string $resource = ForexEventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
