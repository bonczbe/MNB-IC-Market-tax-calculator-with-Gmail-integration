<?php

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Config;

if (Config::get('schedule.use_mnb_fetcher')) {
    app(Schedule::class)
        ->command('app:fetch-mnb-rate')
        ->dailyAt('23:00')
        ->weekdays()
        ->timezone('Europe/Budapest');

    app(Schedule::class)
        ->command('app:fetch-mnb-rate')
        ->weeklyOn(1, '10:00')
        ->timezone('Europe/Budapest');

    app(Schedule::class)
        ->command('app:event-extract')
        ->weeklyOn(1, '0:01')
        ->timezone('Europe/Budapest');

    app(Schedule::class)
        ->command('app:holyday-collect')
        ->yearlyOn(1, 1, '00:01')
        ->timezone('Europe/Budapest');
}

if (Config::get('schedule.use_email_fetcher')) {
    app(Schedule::class)
        ->command('app:email-extract')
        ->dailyAt('23:58')
        ->timezone('Europe/Budapest');
}

if (Config::get('schedule.use_yearly_calculator')) {
    app(Schedule::class)
        ->command('app:calculate-tax-by-account-for-year')
        ->yearlyOn(12, 31, '23:59')
        ->timezone('Europe/Budapest');
}
