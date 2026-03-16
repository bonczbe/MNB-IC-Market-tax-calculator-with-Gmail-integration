<?php

namespace App\Console\Commands;

use App\Jobs\CalculateTaxByAccountForYearJob;
use App\Models\User;
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
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        CalculateTaxByAccountForYearJob::dispatch();
        $this->info('Tax calculation for the year!');

        $users = User::query()->pluck('id');

        foreach ($users as $id) {
            Cache::forget('previousYears'.$id);
            Cache::forget('calculatecurrentDate'.$id);
            Cache::forget('grossProfitOfYear'.$id);
            Cache::forget('profitForYear'.$id);
            Cache::forget('profitForTheWeek'.$id);
        }

    }
}
