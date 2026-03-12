<?php

namespace App\Repositories;

use App\Models\BrokerAccount;
use Carbon\Carbon;

class BrokerAccountRepository
{
    public function __construct() {}

    public function getAccountsWithYearlyTransactionsStatusesAndTax(Carbon $currentYear, Carbon $startOfYear, Carbon $endOfYear)
    {
        return BrokerAccount::query()
            ->with([
                'accountTransactions' => function ($query) use ($startOfYear, $endOfYear) {
                    $query->whereBetween('date', [$startOfYear, $endOfYear]);
                },
                'dailyStatuses' => function ($query) use ($startOfYear, $endOfYear) {
                    $query->whereBetween('date', [$startOfYear, $endOfYear]);
                },
                'yearlyTaxCalculations' => function ($query) use ($currentYear) {
                    $query->where('tax_year', $currentYear->copy()->subYear()->format('Y'));
                },
            ])
            ->get();
    }

    public function getAll()
    {
        return BrokerAccount::query()->get();
    }
}
