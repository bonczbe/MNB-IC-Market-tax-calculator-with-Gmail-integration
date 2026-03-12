<?php

namespace App\Repositories;

use App\Models\EmailExtract;

class EmailExtractRepository
{
    public function __construct() {}

    public function chunkedUpsert(array $multipleDatas, array $uniqueBy)
    {
        return EmailExtract::upsert($multipleDatas, uniqueBy: $uniqueBy);
    }
}
