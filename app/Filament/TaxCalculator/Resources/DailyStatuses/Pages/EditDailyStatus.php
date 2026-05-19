<?php

namespace App\Filament\TaxCalculator\Resources\DailyStatuses\Pages;

use App\Filament\TaxCalculator\Resources\DailyStatuses\DailyStatusResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDailyStatus extends EditRecord
{
    protected static string $resource = DailyStatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
