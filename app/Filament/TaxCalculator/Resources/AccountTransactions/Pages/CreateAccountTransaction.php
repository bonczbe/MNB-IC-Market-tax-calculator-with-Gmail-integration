<?php

namespace App\Filament\TaxCalculator\Resources\AccountTransactions\Pages;

use App\Filament\TaxCalculator\Resources\AccountTransactions\AccountTransactionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAccountTransaction extends CreateRecord
{
    protected static string $resource = AccountTransactionResource::class;
}
