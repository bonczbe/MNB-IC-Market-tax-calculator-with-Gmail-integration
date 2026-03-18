<?php

namespace App\Services;

use App\DTOs\HolyDayDTO;
use App\Repositories\HolyDayRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class HolydayService
{
    public function __construct(private readonly HolyDayRepository $holy_day_repository) {}

    public function fetchHolyDays()
    {

        try {
            $response = Http::timeout(5)
                ->retry(2, 5000, function ($exception, $request) {
                    return $exception instanceof ConnectionException
                        || ($exception instanceof RequestException
                            && $exception->response?->serverError());
                })
                ->get('https://api.massive.com/v1/marketstatus/upcoming', [
                    'apiKey' => config('services.massive.key'),
                ])
                ->throw()
                ->json();

            $res = array_map(fn ($day) => [
                'date' => $day['date'],
                'name' => $day['name'],
                'status' => $day['status'],
            ], $response);

            $this->holy_day_repository->upsert($res, uniqueBy: ['date']);

        } catch (Exception $e) {
            Log::alert('Holyday fetch went wrong', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw new RuntimeException('Holyday fetch failed: '.$e->getMessage(), 0, $e);
        }
    }

    public function getAndMapTodaysHolyDays($date)
    {
        return $this->holy_day_repository->getHolyDaysForDay($date)->map(fn ($day) => new HolyDayDTO($day->date, $day->name, $day->status));
    }
}
