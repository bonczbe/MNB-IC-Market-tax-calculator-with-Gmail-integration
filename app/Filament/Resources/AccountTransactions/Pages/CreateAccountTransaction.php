<?php

namespace App\Filament\Resources\AccountTransactions\Pages;

use App\Filament\Resources\AccountTransactions\AccountTransactionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAccountTransaction extends CreateRecord
{
    protected static string $resource = AccountTransactionResource::class;
}
