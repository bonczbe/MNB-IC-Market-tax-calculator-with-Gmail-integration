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

    public function getWeeklyChartData()
    {
        $now = Carbon::now();
        $startOfWeek = $now->copy()->startOfWeek();
        $endOfWeek = $now->copy()->endOfWeek();
        $user = auth()->user()->id;

        $statuses = $this->dailyStatusRepository->getBetweenDatesByUserId($user, $startOfWeek, $endOfWeek);

        $period = CarbonPeriod::create($startOfWeek, $endOfWeek);

        $data = [];

        foreach ($period as $date) {

            $rates = $this->rateRepository->getRatesByDate($date);

            $dateFormat = $date->format('l');

            if ($dateFormat == 'Saturday' || $dateFormat == 'Sunday') {
                continue;
            }

            $records = $statuses->where('date', $date);

            if (count($records) == 0) {
                $data[$dateFormat] = 0;
            }

            $sum = 0;

            foreach ($records as $record) {
                $sum += $record->balance * ($rates[$record->currency] ?? 1);
            }

            $data[$dateFormat] = $sum;
        }

        return $data;
    }

    public function getYearlyChartData(string $year)
    {
        $now = ($year == 'current_year') ? Carbon::now() : Carbon::parse($year.'-01-01');
        $startOfYear = $now->copy()->startOfYear();
        $endOfYear = ($year == 'current_year') ? $now->copy()->subDay(1) : $now->copy()->endOfYear();
        $user = auth()->user()->id;

        $statuses = $this->dailyStatusRepository->getBetweenDatesByUserId($user, $startOfYear, $endOfYear);

        $period = CarbonPeriod::create($startOfYear, $endOfYear);

        $data = [];

        foreach ($period as $date) {
            $dateFormat = $date->copy()->format('l');

            if ($dateFormat == 'Saturday' || $dateFormat == 'Sunday') {
                continue;
            }

            $rates = $this->rateRepository->getRatesByDate($date);

            $records = $statuses->where('date', $date);

            if (count($records) == 0) {
                $data[$date->format('m-d')] = 0;
            }

            $sum = 0;

            foreach ($records as $record) {
                $sum += $record->balance * ($rates[$record->currency] ?? 1);
            }

            $data[$date->format('m-d')] = $sum;
        }

        return $data;
    }

    public function isWeekend(Carbon $date): bool
    {
        return $date->isWeekend();

    }

    public function getYearsForUserExceptCurrent(): array
    {

        $userId = auth()->user()->id;

        return DailyStatus::selectRaw('YEAR(date) as year')
            ->whereRaw('YEAR(date) != ?', [now()->year])
            ->whereHas('broker', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year', 'year')
            ->toArray();
    }
}
