<?php

namespace App\Filament\Resources\ForexEvents\Pages;

use App\Filament\Resources\ForexEvents\ForexEventResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListForexEvents extends ListRecords
{
    protected static string $resource = ForexEventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
