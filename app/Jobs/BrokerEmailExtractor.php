<?php

namespace App\Jobs;

use App\Services\EmailExtractorService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;

class BrokerEmailExtractor implements ShouldQueue
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
    public function handle(EmailExtractorService $email_extractor_service): void
    {
        $lock = Cache::lock('email-extract-lock', 5);

        if (! $lock->get()) {
            return;
        }

        try {
            $email_extractor_service->extractAndSaveEmail();
        } finally {
            $lock->release();
        }
    }
}
