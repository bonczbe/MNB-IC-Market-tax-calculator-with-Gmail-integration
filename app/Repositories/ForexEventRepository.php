<?php

namespace App\Repositories;

use App\Models\ForexEvent;
use Carbon\Carbon;

class ForexEventRepository
{
    public function __construct() {}

    public function upsert(array $multipleDatas, array $uniqueBy)
    {
        return ForexEvent::upsert($multipleDatas, uniqueBy: $uniqueBy);
    }

    public function getDayEvents(Carbon $date)
    {
        return ForexEvent::query()
            ->where('date', '>=', $date->copy()->format('Y-m-d H:i'))
            ->where('date', '<=', $date->copy()->endOfDay()->format('Y-m-d H:i'))
            ->orderBy('date')
            ->get();
    }
}
