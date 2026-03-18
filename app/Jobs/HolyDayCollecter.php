<?php

namespace App\Jobs;

use App\Services\HolydayService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class HolyDayCollecter implements ShouldQueue
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
    public function handle(HolydayService $holyday_service): void
    {
        $holyday_service->fetchHolyDays();
    }
}
