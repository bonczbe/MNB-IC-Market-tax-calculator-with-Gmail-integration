<?php

namespace App\Console\Commands;

use App\Jobs\CalculateTaxByAccountForYearJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class CalculateTaxByAccountForYear extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:calculate-tax-by-account-for-year';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate yearly tax obligations for all broker accounts based on daily profit/loss data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $lock = Cache::lock('calculate-tax-by-account-for-year-lock', 5);

        if (! $lock->get()) {
            $this->info('Another instance of the command is already running. Exiting.');

            return;
        }
        try {
            CalculateTaxByAccountForYearJob::dispatch();
            $this->info('Tax calculation for the year job dispatched!');
        } finally {
            $lock->release();
        }
    }
}
