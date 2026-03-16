<?php

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

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
}
if (Config::get('schedule.use_email_fetcher')) {
    app(Schedule::class)
        ->command('app:email-extract')
        ->dailyAt('23:45')
        ->weekdays()
        ->timezone('Europe/Budapest');
}
if (Config::get('schedule.use_yearly_calculator')) {
    app(Schedule::class)
        ->command('app:calculate-tax-by-account-for-year')
        ->yearlyOn(12, 31, '23:57')
        ->timezone('Europe/Budapest');
}
