<?php

namespace App\Jobs;

use App\Services\HolydayService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;

class HolyDayCollecter implements ShouldQueue
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
    public function handle(HolydayService $holyday_service): void
    {
        $lock = Cache::lock('holyday-collect-lock', 5);
        if (! $lock->get()) {

            return;
        }
        try {
            $holyday_service->fetchHolyDays();
        } finally {
            $lock->release();
        }
    }
}
