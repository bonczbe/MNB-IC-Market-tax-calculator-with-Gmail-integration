<?php

namespace App\Jobs;

use App\Services\ForexEventService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;

class EventExtractor implements ShouldQueue
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
    public function handle(ForexEventService $forex_event_service): void
    {

        $lock = Cache::lock('event-extract-lock', 5);
        if (! $lock->get()) {

            return;
        }
        try {
            $forex_event_service->extractForexEvents();
        } finally {
            $lock->release();
        }
    }
}
