<?php

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

app(Schedule::class)
    ->command('app:fetch-mnb-rate')
    ->dailyAt('23:00')
    ->weekdays()
    ->timezone('Europe/Budapest');

app(Schedule::class)
    ->command('app:email-extract')
    ->dailyAt('23:50')
    ->weekdays()
    ->timezone('Europe/Budapest');

app(Schedule::class)
    ->command('app:calculate-tax-by-account-for-year')
    ->yearlyOn(12, 31, '23:57')
    ->timezone('Europe/Budapest');
