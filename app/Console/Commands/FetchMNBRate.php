<?php

namespace App\Console\Commands;

use App\Jobs\MNBRateFetcher;
use Illuminate\Console\Command;

class FetchMNBRate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fetch-mnb-rate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch the latest MNB (Magyar Nemzeti Bank) exchange rates and store them in the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        MNBRateFetcher::dispatch();
        $this->info('MNB rate job dispatched!');
    }
}
