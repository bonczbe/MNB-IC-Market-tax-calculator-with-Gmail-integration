<?php

namespace App\Console\Commands;

use App\Jobs\EventExtractor;
use Illuminate\Console\Command;

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
        EventExtractor::dispatch();
        $this->info('Event extract job dispatched!');
    }
}
