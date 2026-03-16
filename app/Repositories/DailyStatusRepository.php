<?php

namespace App\Repositories;

use App\Models\DailyStatus;
use Carbon\Carbon;

class DailyStatusRepository
{
    public function __construct() {}

    public function firstSmallerDatedStatus(int $brokerId, Carbon $date)
    {
        return DailyStatus::query()
            ->where('broker_account_id', $brokerId)
            ->where('date', '<', $date)
            ->orderByDesc('date')
            ->first();
    }

    public function upsert(array $multipleDatas, array $uniqueBy)
    {
        return DailyStatus::upsert($multipleDatas, uniqueBy: $uniqueBy);
    }

    public function getAllDistinctedByKeyValue(string $column)
    {
        return DailyStatus::query()->distinct()->pluck($column, $column);
    }
}
