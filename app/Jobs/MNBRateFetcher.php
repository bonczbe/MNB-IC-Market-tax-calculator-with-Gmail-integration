<?php

namespace App\Jobs;

use App\Services\RateService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;

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

        $lock = Cache::lock('fetch-mnb-rate-lock', 5);

        if (! $lock->get()) {

            return;
        }
        try {
            $rate_service->fetchAndUpsertRatesByMNB();
        } finally {
            $lock->release();
        }
    }
}
