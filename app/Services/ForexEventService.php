<?php

namespace App\Services;

use App\Repositories\ForexEventRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class ForexEventService
{
    public function __construct(private readonly ForexEventRepository $forex_event_repository) {}

    public function extractUsForexEvents(): void
    {
        try {
            $from = Carbon::now()->startOfWeek()->format('Y-m-d') . 'T00:00:00.000Z';
            $to   = Carbon::now()->endOfWeek()->format('Y-m-d') . 'T23:59:59.000Z';

            $url = "https://economic-calendar.tradingview.com/events?from={$from}&to={$to}&countries=US";

            $response = $this->fetchJsonWithCurl($url);
            $items    = $response['result'] ?? [];

            $events = [];

            foreach ($items as $item) {
                $name = $item['title'] ?? null;

                if (! $name || $this->isHoliday($name)) {
                    continue;
                }

                $importance = ($item['importance'] ?? 0) + 2;

                $date = null;
                if (! empty($item['date'])) {
                    $date = Carbon::parse($item['date'])->toDateTimeString();
                }

                $forecast = isset($item['forecast']) && $item['forecast'] !== '' ? (string) $item['forecast'] : null;
                $previous = isset($item['prev'])     && $item['prev'] !== ''     ? (string) $item['prev']     : null;

                $events[] = [
                    'date'      => $date,
                    'name'      => $name,
                    'previouse' => $previous,
                    'forecast'  => $forecast,
                    'importance'=> $importance,
                ];
            }

            $this->forex_event_repository->upsert($events, uniqueBy: ['date', 'name']);

        } catch (Exception $e) {
            Log::alert('Forex events fetch went wrong', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            throw new RuntimeException('Forex events fetch failed: ' . $e->getMessage(), 0, $e);
        }
    }

    private function fetchJsonWithCurl(string $url): array
    {
        $ch = curl_init($url);

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_HTTPHEADER     => [
                'Origin: https://www.tradingview.com',
                'Referer: https://www.tradingview.com/',
                'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123.0.0.0 Safari/537.36',
                'Accept: application/json',
            ],
        ]);

        $body = curl_exec($ch);

        if ($body === false) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new RuntimeException('cURL error: ' . $error);
        }

        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($status >= 400) {
            throw new RuntimeException('HTTP error status: ' . $status);
        }

        $data = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException('JSON decode error: ' . json_last_error_msg());
        }

        return $data;
    }

    private function isHoliday(string $name): bool
    {
        $lowerName = mb_strtolower($name, 'UTF-8');

        $holidayKeywords = [
            'holiday', 'christmas', 'new year', 'easter', 'thanksgiving',
            'independence day', 'labor day', 'labour day', 'memorial day',
            'good friday', 'boxing day', 'all saints', 'epiphany',
            'day off', 'market closed', 'early close',
        ];

        foreach ($holidayKeywords as $kw) {
            if (str_contains($lowerName, $kw)) {
                return true;
            }
        }

        return false;
    }

    public function getEventsByDate($date)
    {
        return $this->forex_event_repository->getDayEvents($date);
    }
}