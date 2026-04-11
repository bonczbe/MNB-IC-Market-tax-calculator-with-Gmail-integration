<?php

namespace App\Services;

use App\Models\DailyStatus;
use App\Repositories\DailyStatusRepository;
use App\Repositories\RateRepository;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class ChartService
{
    public function __construct(private readonly DailyStatusRepository $dailyStatusRepository, private readonly RateRepository $rateRepository) {}

    public function getWeeklyChartData(): array
    {
        $now = Carbon::now();
        $startOfWeek = $now->copy()->startOfWeek();
        $endOfWeek = $now->copy()->endOfWeek();

        [$statuses, $period] = $this->setupPeriodAndStatuses($startOfWeek, $endOfWeek);

        return $this->calculateChartData($period, $statuses, 'l');
    }

    public function getYearlyChartData(string $year): array
    {
        $now = ($year == 'current_year') ? Carbon::now() : Carbon::parse($year.'-01-01');
        $startOfYear = $now->copy()->startOfYear();
        $endOfYear = ($year == 'current_year') ? $now->copy()->subDay(1) : $now->copy()->endOfYear();

        [$statuses, $period] = $this->setupPeriodAndStatuses($startOfYear, $endOfYear);

        return $this->calculateChartData($period, $statuses, 'm-d');
    }

    public function getYearsForUserExceptCurrent(): array
    {

        $userId = auth()->user()->id;

        return $this->dailyStatusRepository->getYearsForUserExceptCurrent($userId);
    }

    private function getRatesAndStatusRecordsForDate(Carbon $date, $statuses): array
    {
        $rates = $this->rateRepository->getRatesByDate($date);

        $records = $statuses->where('date', $date);

        return [$rates, $records];
    }

    private function calculateChartData($period, $statuses, $format): array
    {

        $data = [];

        foreach ($period as $date) {

            if ($date->isWeekend()) {
                continue;
            }

            [$rates, $records] = $this->getRatesAndStatusRecordsForDate($date, $statuses);

            $dateFormat = $date->format($format);

            if (count($records) == 0) {
                $data[$dateFormat] = 0;
            }

            $data[$dateFormat] = $this->calcSum($records, $rates);
        }

        return $data;
    }

    private function calcSum($records, $rates)
    {

        $sum = 0;

        foreach ($records as $record) {
            $sum += $record->balance * ($rates[$record->currency] ?? 1);
        }

        return $sum;
    }

    private function setupPeriodAndStatuses($startDate, $endDate): array
    {
        $user = auth()->user()->id;

        $statuses = $this->dailyStatusRepository->getBetweenDatesByUserId($user, $startDate, $endDate);

        $period = CarbonPeriod::create($startDate, $endDate);

        return [$statuses, $period];

    }
}
