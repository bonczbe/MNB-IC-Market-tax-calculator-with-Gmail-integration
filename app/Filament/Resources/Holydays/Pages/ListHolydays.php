<?php

namespace App\Filament\Resources\Holydays\Pages;

use App\Filament\Resources\Holydays\HolydayResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListHolydays extends ListRecords
{
    protected static string $resource = HolydayResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
