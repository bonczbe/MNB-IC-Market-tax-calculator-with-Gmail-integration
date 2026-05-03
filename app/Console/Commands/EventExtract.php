<?php

namespace App\Console\Commands;

use App\Jobs\EventExtractor;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class EventExtract extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:event-extract';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Extract forex events from website.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $lock = Cache::lock('event-extract-lock', 5);
        if (! $lock->get()) {
            $this->info('Another instance of the command is already running. Exiting.');

            return;
        }
        try {
            EventExtractor::dispatch();
            $this->info('Event extract job dispatched!');
        } finally {
            $lock->release();
        }
    }
}
