<?php

namespace App\Repositories;

use App\Models\Holyday;

class HolyDayRepository
{
    public function __construct() {}

    public function upsert(array $multipleDatas, array $uniqueBy)
    {
        return Holyday::upsert($multipleDatas, uniqueBy: $uniqueBy);
    }

    public function getHolyDaysForDay($day)
    {
        return Holyday::query()
            ->where('date', '==', $day->copy()->format('Y-m-d'))
            ->get();
    }
}
