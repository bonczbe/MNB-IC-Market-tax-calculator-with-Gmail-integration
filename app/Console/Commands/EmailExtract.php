<?php

namespace App\Console\Commands;

use App\Jobs\BrokerEmailExtractor;
use Illuminate\Console\Command;

class EmailExtract extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:email-extract';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        BrokerEmailExtractor::dispatch();
        $this->info('Email extract job dispatched!');
    }
}
