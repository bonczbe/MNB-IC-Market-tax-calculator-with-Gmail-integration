<?php

use App\Models\ForexEvent;
use Carbon\Carbon;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;

Route::get('/', fn () => redirect('/admin'))->name('home');

if (app()->environment('local')) {
    Route::get('/test', function () {

            //try {
        $url = 'https://sslecal2.forexprostools.com/?columns=exc_flags,exc_currency,exc_importance,exc_actual,exc_forecast,exc_previous&features=datepicker,timezone&countries=5&calType=day&timeZone=15&lang=1';

        $ch = curl_init($url);

        $headers = [
            'Referer: https://www.investing.com/economic-calendar/',
            'User-Agent: Mozilla/5.0 (iPhone; CPU iPhone OS 16_3_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.3 Mobile/15E148 Safari/604.1',
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'Accept-Language: en-US,en;q=0.5',
            'Connection: keep-alive',
        ];

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_TIMEOUT        => 15,
        ]);

        $html = curl_exec($ch);

        $dom = new DOMDocument();
        @$dom->loadHTML($html);
        $xpath = new DOMXPath($dom);

        $rows = $xpath->query('//tr[starts-with(@id, "eventRowId_")]');

        foreach ($rows as $row) {
            $timestamp = $row->getAttribute('event_timestamp');
            $date = null;
            if (!empty($timestamp)) {
                $date = Carbon::createFromFormat('Y-m-d H:i:s', $timestamp)->toDateTimeString();
            }

            $nameNodeList = $xpath->query('.//td[contains(@class,"event")]', $row);
            $name = $nameNodeList->length ? trim($nameNodeList->item(0)->nodeValue) : null;

            $actualNodeList   = $xpath->query('.//td[contains(@class,"act")]', $row);
            $forecastNodeList = $xpath->query('.//td[contains(@class,"fore")]', $row);
            $previousNodeList = $xpath->query('.//td[contains(@class,"prev")]', $row);

            $forecast = $forecastNodeList->length ? trim($forecastNodeList->item(0)->nodeValue) : null;
            $previous = $previousNodeList->length ? trim($previousNodeList->item(0)->nodeValue) : null;

            $forecast = ($forecast === "\xc2\xa0" || $forecast === '&nbsp;' || $forecast === '') ? null : $forecast;
            $previous = ($previous === "\xc2\xa0" || $previous === '&nbsp;' || $previous === '') ? null : $previous;

            if (!$name) {
                continue;
            }

            dd($name);

            /*ForexEvent::create([
                'date'      => $date,
                'name'      => $name,
                'status'    => $actual,
                'previouse' => $previous,
                'forecast'  => $forecast,
            ]);*/
        }

    /*} catch (Exception $e) {
        Log::alert('Forex events fetch went wrong', [
            'message' => $e->getMessage(),
            'trace'   => $e->getTraceAsString(),
        ]);

        throw new RuntimeException('Forex events fetch failed: '.$e->getMessage(), 0, $e);
    }*/

        return 'ok';
    })->name('test');
}
