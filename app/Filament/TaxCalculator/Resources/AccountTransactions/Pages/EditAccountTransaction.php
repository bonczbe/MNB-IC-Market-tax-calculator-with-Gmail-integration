<?php

namespace App\Filament\TaxCalculator\Resources\AccountTransactions\Pages;

use App\Filament\TaxCalculator\Resources\AccountTransactions\AccountTransactionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAccountTransaction extends EditRecord
{
    protected static string $resource = AccountTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
