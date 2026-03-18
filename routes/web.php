<?php

use App\Http\Controllers\ForexEventController;
use App\Http\Controllers\HolyDayController;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

$response = Http::get('http://example.com');

Route::get('/', fn () => redirect('/admin'))->name('home');

Route::get('/holyday-on-today', [HolyDayController::class, 'getTodayHolyDay']);
Route::get('/events-on-today', [ForexEventController::class, 'getTodaysEvents']);

if (app()->environment('local')) {

    Route::get('/test', function () {
        return 'ok';
    })->name('test');

}
