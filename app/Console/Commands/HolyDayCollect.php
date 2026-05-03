<?php

namespace App\Console\Commands;

use App\Jobs\HolyDayCollecter;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class HolyDayCollect extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:holyday-collect';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get holydays from api.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $lock = Cache::lock('holyday-collect-lock', 5);
        if (! $lock->get()) {
            $this->info('Another instance of the command is already running. Exiting.');

            return;
        }
        try {
            HolyDayCollecter::dispatch();
            $this->info('Get holydays job dispatched!');
        } finally {
            $lock->release();
        }
    }
}
