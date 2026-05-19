<?php

use App\Http\Controllers\ForexEventController;
use App\Http\Controllers\HolyDayController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect('/taxCalculator'))->name('home');

Route::get('/holydays', [HolyDayController::class, 'getByDate']);
Route::get('/events', [ForexEventController::class, 'getByDate']);

if (app()->environment('local')) {

    Route::get('/test', function () {

        return 'ok';
    })->name('test');

}
