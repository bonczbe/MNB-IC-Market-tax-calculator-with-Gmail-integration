<?php

namespace App\Services;

use App\Repositories\ForexEventRepository;
use Carbon\Carbon;
use DOMDocument;
use DOMXPath;
use Exception;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class ForexEventService
{
    public function __construct(private readonly ForexEventRepository $forex_event_repository) {}

    public function extractUsForexEvents()
    {

        try {
            $url = 'https://sslecal2.forexprostools.com/?columns=exc_flags,exc_currency,exc_importance,exc_actual,exc_forecast,exc_previous&features=datepicker,timezone&countries=5&calType=week&timeZone=16&lang=1';

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
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_TIMEOUT => 15,
            ]);

            $html = curl_exec($ch);

            $dom = new DOMDocument;
            @$dom->loadHTML($html);
            $xpath = new DOMXPath($dom);

            $rows = $xpath->query('//tr[starts-with(@id, "eventRowId_")]');

            $evets = [];

            foreach ($rows as $row) {
                $timestamp = $row->getAttribute('event_timestamp');
                $date = null;
                if (! empty($timestamp)) {
                    $date = Carbon::createFromFormat('Y-m-d H:i:s', $timestamp)->toDateTimeString();
                }

                $nameNodeList = $xpath->query('.//td[contains(@class,"event")]', $row);
                $name = $nameNodeList->length ? trim($nameNodeList->item(0)->nodeValue) : null;

                $forecastNodeList = $xpath->query('.//td[contains(@class,"fore")]', $row);
                $previousNodeList = $xpath->query('.//td[contains(@class,"prev")]', $row);
                $sentimentNodeList = $xpath->query('.//td[contains(@class,"sentiment")]', $row);

                $forecast = $forecastNodeList->length ? trim($forecastNodeList->item(0)->nodeValue) : null;
                $previous = $previousNodeList->length ? trim($previousNodeList->item(0)->nodeValue) : null;

                $forecast = ($forecast === "\xc2\xa0" || $forecast === '&nbsp;' || $forecast === '') ? null : $forecast;
                $previous = ($previous === "\xc2\xa0" || $previous === '&nbsp;' || $previous === '') ? null : $previous;

                $importance = $this->extractImportance($sentimentNodeList, $xpath);

                if (! $name) {
                    continue;
                }

                $isHoliday = $this->isHoliday($name);

                if ($isHoliday) {
                    continue;
                }

                $evets[] = [
                    'date' => $date,
                    'name' => $name,
                    'previouse' => $previous,
                    'forecast' => $forecast,
                    'importance' => $importance,
                ];
            }

            $this->forex_event_repository->upsert($evets, uniqueBy: ['date', 'name']);

        } catch (Exception $e) {
            Log::alert('Forex events fetch went wrong', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw new RuntimeException('Forex events fetch failed: '.$e->getMessage(), 0, $e);
        }
    }

    private function extractImportance($sentimentNodeList, $xpath)
    {
        $importance = 1;
        if ($sentimentNodeList->length) {
            $sentimentTd = $sentimentNodeList->item(0);
            $icons = $xpath->query('.//i', $sentimentTd);
            $filled = 0;
            foreach ($icons as $icon) {
                $cls = $icon->getAttribute('class');
                if (str_contains($cls, 'FullBullishIcon')) {
                    $filled++;
                }
            }
            $importance = $filled ?: 1;
        }

        return $importance;
    }

    private function isHoliday($name)
    {

        $lowerName = mb_strtolower($name, 'UTF-8');

        $holidayKeywords = [
            'holiday',
            'christmas',
            'new year',
            'easter',
            'thanksgiving',
            'independence day',
            'labor day',
            'labour day',
            'memorial day',
            'good friday',
            'boxing day',
            'all saints',
            'epiphany',
            'day off',
            'market closed',
            'early close',
        ];

        foreach ($holidayKeywords as $kw) {
            if (str_contains($lowerName, $kw)) {
                return true;
            }
        }

        return false;
    }

    public function getTodayEventsAndMap()
    {
        $now = Carbon::now();

        return $this->forex_event_repository->getDayEvents($now);
    }
}
