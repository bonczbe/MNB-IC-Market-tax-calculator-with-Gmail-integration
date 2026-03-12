<?php

namespace App\Filament\Widgets;

use App\Models\BrokerAccount;
use App\Models\DailyStatus;
use App\Models\Rate;
use App\Models\YearlyTaxCalculation;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;

class ProfitStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {

        $currentYear = Carbon::now();
        $cards = [];

        $cards = [
            $this->calculateCurrentYear($currentYear),
            ...$this->calculatePreviouseYears($currentYear),
        ];

        return $cards;

    }

    private function calculatePreviouseYears($currentYear)
    {
        $previouseCards = [];

        $previouseYears = Cache::remember('previouseYears',3600 ,fn()=>YearlyTaxCalculation::query()
            ->where('tax_year', '<>', $currentYear->copy()->format('Y'))
            ->orderBy('tax_year', 'desc')
            ->distinct()
            ->pluck('tax_year'));

        foreach ($previouseYears as $prevYear) {
            $yearTax = 0;
            $yearDatas = Cache::remember('yearDatas'.$prevYear,3600 ,fn()=>YearlyTaxCalculation::query()
                ->where('tax_year', $prevYear)
                ->with('broker')
                ->get());

            foreach ($yearDatas as $yd) {
                $yearTax += $yd->tax_amount;
            }

            $previouseCards[] =
            Stat::make(
                "{$prevYear} Tax",
                number_format(ceil($yearTax)).' '.env('BASE_CURRENCY', 'HUF')
            );
        }

        return $previouseCards;
    }

    private function calculateCurrentYear($currentYear)
    {

        return
            Stat::make(
                "{$currentYear->copy()->format('Y')} Tax",
                Cache::remember('calculateCurrentYear',3600 , function () use ($currentYear) {
                    $startOfYear = $currentYear->copy()->startOfYear();
                    $endOfYear = $currentYear->copy()->endOfYear();
                    $tax = 0;

                    $brokers = BrokerAccount::query()
                        ->with([
                            'accountTransactions' => function ($query) use ($startOfYear, $endOfYear) {
                                $query->whereBetween('date', [$startOfYear, $endOfYear]);
                            },
                            'dailyStatuses' => function ($query) use ($startOfYear, $endOfYear) {
                                $query->whereBetween('date', [$startOfYear, $endOfYear]);
                            },
                            'yearlyTaxCalculations' => function ($query) use ($currentYear) {
                                $query->where('tax_year', $currentYear->copy()->subYear()->format('Y'));
                            },
                        ])
                        ->get();

                    $ratesOfTheYear = Rate::query()
                        ->whereBetween('date', [$startOfYear, $endOfYear])
                        ->get();

                    foreach ($brokers as $broker) {
                        $allProfitInExchangedCurrency = 0;
                        $allProfitInOriginalCurrancy = 0;
                        $starterBalance = $broker->starting_balance;

                        $ratesForBroker = $ratesOfTheYear
                            ->filter(fn ($rate) => $rate->base_currency == $broker->broker_currency);

                        $lastBeforeTheYear = DailyStatus::query()
                            ->where('broker_account_id', $broker->id)
                            ->where('date', '<', $startOfYear)
                            ->orderByDesc('date')
                            ->first();

                        $previousStatus = null;

                        foreach ($broker->dailyStatuses as $status) {
                            $depositAndWithdrawSum = 0;
                            $rate = $ratesForBroker
                                ->filter(fn ($rate) => $status->date == $rate->date)
                                ->first();

                            $transactions = $broker->accountTransactions->filter(fn ($act) => $act->date == $status->date);

                            foreach ($transactions as $transaction) {
                                $value = $transaction->amount;
                                if ($transaction->type == 'withdrawal') {
                                    $value *= -1;
                                }
                                $depositAndWithdrawSum += $value;
                            }

                            $dailyProfitOrLoss = ($previousStatus !== null) ?
                                $status->balance - ($previousStatus->balance + $depositAndWithdrawSum) :
                                (($lastBeforeTheYear !== null) ?
                                    $status->balance - ($lastBeforeTheYear->balance + $depositAndWithdrawSum) :
                                    $status->balance - ($starterBalance + $depositAndWithdrawSum)
                                );

                            $allProfitInOriginalCurrancy += $dailyProfitOrLoss;
                            $allProfitInExchangedCurrency += $dailyProfitOrLoss * $rate->rate;

                            $previousStatus = $status;
                        }

                        $previouseYear = $broker->yearlyTaxCalculations->first();

                        if ($previouseYear !== null) {
                            $allProfitInExchangedCurrency -= $previouseYear->unused_loss;
                        }

                        $tax += ceil($allProfitInExchangedCurrency * 0.15);

                    }

                    return number_format(ceil($tax)).' '.env('BASE_CURRENCY', 'HUF');
                })
            );
    }
}
