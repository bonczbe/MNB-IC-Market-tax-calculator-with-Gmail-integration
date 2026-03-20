<?php

namespace App\Repositories;

use App\Models\BrokerAccount;
use Carbon\Carbon;

class BrokerAccountRepository
{
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
            ->where('user_id', auth()->user()->id)
            ->get();
    }

    public function getAll()
    {
        return BrokerAccount::query()->get();
    }

    public function getAllForUserId(int $userId)
    {
        return BrokerAccount::query()->where('user_id', $userId)->get();
    }

    public function getAllDistinctedByKeyValue(string $column)
    {
        return BrokerAccount::query()->distinct()->pluck($column, $column);
    }
}
