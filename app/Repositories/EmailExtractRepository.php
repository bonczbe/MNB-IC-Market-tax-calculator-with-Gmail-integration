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
}
