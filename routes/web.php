<?php

use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect('/admin'))->name('home');

if (app()->environment('local')) {
    Route::get('/test', function () {

        return 'ok';
    })->name('test');
}
