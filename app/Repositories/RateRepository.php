<?php

namespace App\Repositories;

use App\Models\Rate;
use Carbon\Carbon;

class RateRepository
{
    public function __construct() {}

    public function getRatesBetweenDates(Carbon $start, Carbon $end)
    {
        return Rate::query()
            ->whereBetween('date', [$start, $end])
            ->get();
    }

    public function chunkedUpsert(array $multipleDatas, array $uniqueBy)
    {
        return Rate::upsert($multipleDatas, uniqueBy: $uniqueBy);
    }
}
