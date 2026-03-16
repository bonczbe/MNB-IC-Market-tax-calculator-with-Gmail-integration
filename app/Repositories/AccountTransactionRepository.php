<?php

namespace App\Repositories;

use App\Models\AccountTransaction;

class AccountTransactionRepository
{
    public function __construct() {}

    public function getAllDistinctedByKeyValue(string $column)
    {
        return AccountTransaction::query()->distinct()->pluck($column, $column);
    }
}
