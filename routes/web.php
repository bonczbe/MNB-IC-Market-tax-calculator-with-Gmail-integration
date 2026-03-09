<?php

use Carbon\Carbon;
use DirectoryTree\ImapEngine\Mailbox;
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

    $mailbox = new Mailbox(config('imap.default'));

    $inbox = $mailbox->inbox();
    $messages = $inbox->messages()
        ->since(Carbon::now()->subDays(3))
        ->before(today()->addDay())
        ->from('support@icmarkets.eu')
        ->subject('Daily Confirmation')
        ->withBody()
        ->withBodyStructure()
        ->get();

    foreach ($messages as $message) {
        $raw = $message->bodyPart('1');
        $html = base64_decode($raw);

        $dom = new DOMDocument;
        @$dom->loadHTML($html);
        $xpath = new DOMXPath($dom);

        $filterNumber = $xpath->query('//b[text()="52776665"]');

        if ($filterNumber->length > 0) {

            $query = '//tr/td[b[normalize-space(text())="Total:"]]/following-sibling::td[1]/b';
            $nodeList = $xpath->query($query);
            $rawTotal = $nodeList->item(0)->nodeValue;
            $total = floatval(str_replace(' ', '', trim($rawTotal)));

            dd($total);
        }

    }

    return 'ok';
})->name('home');
