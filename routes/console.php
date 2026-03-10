<?php

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

app(Schedule::class)
    ->command('app:fetch-mnb-rate')
    ->dailyAt('12:00')
    ->weekdays()
    ->timezone('Europe/Budapest');

app(Schedule::class)
    ->command('app:email-extract')
    ->dailyAt('23:50')
    ->weekdays()
    ->timezone('Europe/Budapest');
