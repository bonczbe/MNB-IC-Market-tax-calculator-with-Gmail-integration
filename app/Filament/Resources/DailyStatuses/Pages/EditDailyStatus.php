<?php

namespace App\Filament\Resources\DailyStatuses\Pages;

use App\Filament\Resources\DailyStatuses\DailyStatusResource;
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
