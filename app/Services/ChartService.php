<?php

namespace App\Services;

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

            $records = $statuses->where('date', $date);

            if (count($records) == 0) {
                $data[$date->format('l')] = 0;
            }

            $sum = 0;

            foreach ($records as $record) {
                $sum += $record->balance * ($rates[$record->currency] ?? 1);
            }

            $data[$date->format('l')] = $sum;
        }

        return $data;
    }

    public function getYearlyChartData()
    {
        $now = Carbon::now();
        $startOfWeek = $now->copy()->startOfYear();
        $endOfWeek = $now->copy();
        $user = auth()->user()->id;

        $statuses = $this->dailyStatusRepository->getBetweenDatesByUserId($user, $startOfWeek, $endOfWeek);

        $period = CarbonPeriod::create($startOfWeek, $endOfWeek);

        $data = [];

        foreach ($period as $date) {
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
}
