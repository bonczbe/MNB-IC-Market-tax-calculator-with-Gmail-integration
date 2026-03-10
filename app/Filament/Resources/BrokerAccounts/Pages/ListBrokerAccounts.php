<?php

namespace App\Filament\Resources\BrokerAccounts\Pages;

use App\Filament\Resources\BrokerAccounts\BrokerAccountResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBrokerAccounts extends ListRecords
{
    protected static string $resource = BrokerAccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
