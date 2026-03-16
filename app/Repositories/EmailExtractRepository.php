<?php

namespace App\Repositories;

use App\Models\EmailExtract;

class EmailExtractRepository
{
    public function __construct() {}

    public function upsert(array $multipleDatas, array $uniqueBy)
    {
        return EmailExtract::upsert($multipleDatas, uniqueBy: $uniqueBy);
    }

    public function getAllDistinctedByKeyValue(string $column)
    {
        return EmailExtract::query()->distinct()->pluck($column, $column);
    }
}
