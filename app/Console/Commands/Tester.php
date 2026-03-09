<?php

namespace App\Console\Commands;

use App\Jobs\MNBRateFetcher;
use Illuminate\Console\Command;

class Tester extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:tester';

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
        MNBRateFetcher::dispatch();
        $this->info('Job dispatched!');
    }
}
