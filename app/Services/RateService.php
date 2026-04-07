<?php

namespace App\Services;

use App\Repositories\RateRepository;
use Carbon\Carbon;
use DOMDocument;
use DOMXPath;
use Exception;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class RateService
{
    public function __construct(private readonly RateRepository $rate_repository) {}

    public function fetchAndUpsertRatesByMNB()
    {
        try {
            $currencies = $this->rate_repository->getAllCurrency();

            $html = file_get_contents('https://www.mnb.hu/en/arfolyamok');

            $dom = new DOMDocument;
            @$dom->loadHTML($html);
            $xpath = new DOMXPath($dom);

            $caption = $xpath->query('//caption[@class="ttl ttl-s"]');
            $rows = $xpath->query('//tr[td[@class="fw-b"]]');

            $now = Carbon::now()->format('Y-m-d');

            $upsertData = [];

            foreach ($rows as $row) {
                $cells = $xpath->query('.//td', $row);

                if ($cells->length >= 4) {
                    $baseCurrency = trim($cells->item(0)->nodeValue);

                    $upsertData[] = [
                        'for_currency' => config('tax.base_currency'),
                        'base_currency' => $baseCurrency,
                        'date' => $now,
                        'unit' => trim($cells->item(2)->nodeValue),
                        'rate' => trim($cells->item(3)->nodeValue),
                    ];
                }
            }

            $this->rate_repository->upsert($upsertData, ['base_currency', 'for_currency', 'date']);

        } catch (Exception $e) {
            Log::alert('Rate fetching went wrong', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw new RuntimeException('MNB rate fetch failed: '.$e->getMessage(), 0, $e);
        }
    }
}
