<?php

namespace App\Repositories;

use App\Models\DailyStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

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

    public function getBetweenDatesByUserId($userId, Carbon $start, Carbon $end, $activeBroker = null)
    {
        return DailyStatus::query()
            ->whereHas('broker', fn (Builder $query) => $query->where('user_id', $userId))
            ->when($activeBroker && $activeBroker !== 0, function (Builder $query, string $activeBroker) {
                $query->where('broker_account_id', $activeBroker);
            })
            ->whereBetween('date', [$start->format('Y-m-d'), $end->format('Y-m-d')])
            ->get(['balance', 'date', 'currency']);
    }

    public function getYearsForUserExceptCurrent(int $userId)
    {
        return DailyStatus::query()
            ->selectRaw('YEAR(date) as year')
            ->whereRaw('YEAR(date) != ?', [now()->year])
            ->whereHas('broker', fn (Builder $query) => $query->where('user_id', $userId))
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year', 'year')
            ->toArray();
    }
}
