<?php

namespace App\Services;

use App\Repositories\RateRepository;
use Carbon\Carbon;
use DOMDocument;
use DOMXPath;

class RateService
{
    public function __construct(private readonly RateRepository $rate_repository) {}

    public function fetchAndUpsertRatesByMNB()
    {
        $html = file_get_contents('https://www.mnb.hu/en/arfolyamok');

        $dom = new DOMDocument;
        @$dom->loadHTML($html);
        $xpath = new DOMXPath($dom);

        $caption = $xpath->query('//caption[@class="ttl ttl-s"]');
        $rows = $xpath->query('//tr[td[@class="fw-b"]]');

        $date = Carbon::createFromFormat('d F Y', explode(': ', $caption->item(0)->nodeValue)[1])
            ->format('Y-m-d');

        $upsertData = [];

        foreach ($rows as $row) {
            $cells = $xpath->query('.//td', $row);

            if ($cells->length >= 4) {

                $upsertData[] = [
                    'for_currency' => env('BASE_CURRENCY', 'HUF'),
                    'base_currency' => trim($cells->item(0)->nodeValue),
                    'date' => $date,
                    'unit' => trim($cells->item(2)->nodeValue),
                    'rate' => trim($cells->item(3)->nodeValue),
                ];
            }
        }
        $this->rate_repository->upsert($upsertData, ['base_currency', 'for_currency', 'date']);
    }
}
