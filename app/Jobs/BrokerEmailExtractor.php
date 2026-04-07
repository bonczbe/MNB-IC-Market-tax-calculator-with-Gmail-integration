<?php

namespace App\Jobs;

use App\Services\EmailExtractorService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

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
        $email_extractor_service->extractAndSaveEmail();
    }
}
