<?php

use App\Models\Rate;
use Carbon\Carbon;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::inertia('/', 'welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';

Route::get('/teszt', function () {

    $html = file_get_contents('https://www.mnb.hu/en/arfolyamok');

    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);
    $caption = $xpath->query('//caption[@class="ttl ttl-s"]');
    $rows = $xpath->query('//tr[td[@class="fw-b"]]');
    $date = Carbon::createFromFormat('d F Y', explode(': ', $caption->item(0)->nodeValue)[1])->format('Y-m-d');

    $upsertData = [];
    foreach ($rows as $row) {
        $cells = $xpath->query('.//td', $row);
        if ($cells->length >= 4) {

            $upsertData[] = [
                'base_currency' => env('BASE_CURRENCY', 'HUF'),
                'for_currency' => trim($cells->item(1)->nodeValue),
                'date' => $date,
                'unit' => trim($cells->item(2)->nodeValue),
                'rate' => trim($cells->item(3)->nodeValue),
            ];
        }
    }
    Rate::upsert($upsertData, ['base_currency', 'for_currency', 'date']);

    return 'ok';
})->name('home');
