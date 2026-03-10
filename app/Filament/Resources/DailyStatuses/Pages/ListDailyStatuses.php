<?php

namespace App\Filament\Resources\DailyStatuses\Pages;

use App\Filament\Resources\DailyStatuses\DailyStatusResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDailyStatuses extends ListRecords
{
    protected static string $resource = DailyStatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
