<?php

namespace App\Console\Commands;

use App\Jobs\HolyDayCollecter;
use Illuminate\Console\Command;

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
        HolyDayCollecter::dispatch();
        $this->info('Get holydays job dispatched!');
    }
}
