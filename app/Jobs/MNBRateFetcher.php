<?php

namespace App\Jobs;

use App\Services\RateService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class MNBRateFetcher implements ShouldQueue
{
    use Queueable;

    public $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        $this->onQueue('high');
    }

    /**
     * Execute the job.
     */
    public function handle(RateService $rate_service): void
    {
        $rate_service->fetchAndUpsertRatesByMNB();
    }
}
