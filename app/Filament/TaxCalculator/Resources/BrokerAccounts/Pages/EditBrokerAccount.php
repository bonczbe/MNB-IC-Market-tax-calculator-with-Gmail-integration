<?php

namespace App\Filament\TaxCalculator\Resources\BrokerAccounts\Pages;

use App\Filament\TaxCalculator\Resources\BrokerAccounts\BrokerAccountResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBrokerAccount extends EditRecord
{
    protected static string $resource = BrokerAccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
