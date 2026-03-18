<?php

namespace App\Jobs;

use App\Services\ForexEventService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class EventExtractor implements ShouldQueue
{
    use Queueable;

    public $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct() {}

    /**
     * Execute the job.
     */
    public function handle(ForexEventService $forex_event_service): void
    {
        $forex_event_service->extractUsForexEvents();
    }
}
