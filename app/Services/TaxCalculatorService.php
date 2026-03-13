<?php

namespace App\Services;

use App\Repositories\BrokerAccountRepository;
use App\Repositories\DailyStatusRepository;
use App\Repositories\RateRepository;
use App\Repositories\YearlyTaxCalculationRepository;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;

class TaxCalculatorService
{
    public function __construct(
        private readonly BrokerAccountRepository $broker_account_repository,
        private readonly DailyStatusRepository $daily_status_repository,
        private readonly RateRepository $rate_repository,
        private readonly YearlyTaxCalculationRepository $yearly_tax_calculation_repository
    ) {}

    public function calculateAllBrokerAccountTaxForYear(Carbon $currentYear)
    {
        $startOfYear = $currentYear->copy()->startOfYear();
        $endOfYear = $currentYear->copy()->endOfYear();

        $brokers = $this->broker_account_repository
            ->getAccountsWithYearlyTransactionsStatusesAndTax($currentYear, $startOfYear, $endOfYear);

        $ratesOfTheYear = $this->rate_repository
            ->getRatesBetweenDates($startOfYear, $endOfYear);

        foreach ($brokers as $broker) {
            $allProfitInExchangedCurrency = 0;
            $starterBalance = $broker->starting_balance;

            $ratesForBroker = $ratesOfTheYear
                ->filter(fn ($rate) => $rate->base_currency == $broker->broker_currency);

            $lastBeforeTheYear = $this->daily_status_repository
                ->firstSmallerDatedStatus($broker->id, $startOfYear);

            $allProfitInExchangedCurrency += $this->calculateYearlyProfitInBaseCurrency(
                $broker,
                $ratesForBroker,
                $lastBeforeTheYear,
                $starterBalance);

            $previouseYear = $broker->yearlyTaxCalculations->first();

            $grossProfit = $allProfitInExchangedCurrency;

            if ($previouseYear !== null) {
                $allProfitInExchangedCurrency -= $previouseYear->unused_loss;
            }

            $tax = ceil($allProfitInExchangedCurrency * config('tax.volume'));

            $unusedLoss = ($grossProfit < 0 && $allProfitInExchangedCurrency < 0) ? $grossProfit : 0;

            $upsertData = [
                'broker_account_id' => $broker->id,
                'tax_year' => $currentYear->copy()->format('Y'),
                'gross_profit' => $grossProfit,
                'loss_carried_forward' => $previouseYear->unused_loss ?? 0,
                'taxable_income' => $allProfitInExchangedCurrency,
                'tax_amount' => $tax,
                'unused_loss' => $unusedLoss,
            ];

            $this->yearly_tax_calculation_repository
                ->upsert($upsertData, ['broker_account_id', 'tax_year']);
        }
    }

    public function calculateAllBrokerAccountTaxForActualYear(Carbon $currentYear)
    {
        $startOfYear = $currentYear->copy()->startOfYear();
        $endOfYear = $currentYear->copy()->endOfYear();
        $tax = 0;

        $brokers = $this->broker_account_repository
            ->getAccountsWithYearlyTransactionsStatusesAndTax($currentYear, $startOfYear, $endOfYear);

        $ratesOfTheYear = $this->rate_repository
            ->getRatesBetweenDates($startOfYear, $endOfYear);

        foreach ($brokers as $broker) {
            $allProfitInExchangedCurrency = 0;
            $starterBalance = $broker->starting_balance;

            $ratesForBroker = $ratesOfTheYear
                ->filter(fn ($rate) => $rate->base_currency == $broker->broker_currency);

            $lastBeforeTheYear = $this->daily_status_repository
                ->firstSmallerDatedStatus($broker->id, $startOfYear);

            $allProfitInExchangedCurrency += $this->calculateYearlyProfitInBaseCurrency(
                $broker,
                $ratesForBroker,
                $lastBeforeTheYear,
                $starterBalance);

            $previouseYear = $broker->yearlyTaxCalculations->first();

            if ($previouseYear !== null) {
                $allProfitInExchangedCurrency -= $previouseYear->unused_loss;
            }

            $tax += ceil($allProfitInExchangedCurrency * config('tax.volume'));

        }

        return number_format(ceil($tax)).' '.config('tax.base_currency');

    }

    public function calculateTaxForPreviouseYears(Carbon $currentYear)
    {

        $previouseCards = [];

        $previouseYears = Cache::remember('previouseYears', 3600, fn () => $this
            ->yearly_tax_calculation_repository
            ->getAllExitingYearsExepctTheGivenDate($currentYear)
        );

        foreach ($previouseYears as $prevYear) {
            $yearTax = 0;
            $yearDatas = Cache::remember('yearDatas'.$prevYear, 3600, fn () => $this
                ->yearly_tax_calculation_repository
                ->getByDate($prevYear)
            );

            foreach ($yearDatas as $yd) {
                $yearTax += $yd->tax_amount;
            }

            $previouseCards[] =
            Stat::make(
                "{$prevYear} Tax",
                number_format(ceil($yearTax)).' '.config('tax.base_currency')
            );
        }

        return $previouseCards;

    }

    private function calculateYearlyProfitInBaseCurrency(
        $broker,
        $ratesForBroker,
        $lastBeforeTheYear,
        $starterBalance,
    ) {
        $allProfitInExchangedCurrency = 0;
        $previousStatus = null;

        foreach ($broker->dailyStatuses as $status) {
            $depositAndWithdrawSum = 0;
            $rate = $ratesForBroker
                ->filter(fn ($rate) => $status->date == $rate->date)
                ->first();

            $transactions = $broker->accountTransactions->filter(fn ($act) => $act->date == $status->date);

            $depositAndWithdrawSum = $this->calculateSumOfTransactions($transactions);

            $dailyProfitOrLoss = ($previousStatus !== null) ?
                $status->balance - ($previousStatus->balance + $depositAndWithdrawSum) :
                (($lastBeforeTheYear !== null) ?
                    $status->balance - ($lastBeforeTheYear->balance + $depositAndWithdrawSum) :
                    $status->balance - ($starterBalance + $depositAndWithdrawSum)
                );

            $allProfitInExchangedCurrency += $dailyProfitOrLoss * ($rate->rate ?? 1);

            $previousStatus = $status;
        }

        return $allProfitInExchangedCurrency;
    }

    private function calculateSumOfTransactions($transactions)
    {
        $sum = 0;
            foreach ($transactions as $transaction) {
                $value = $transaction->amount;
                if ($transaction->type == 'withdrawal') {
                    $value *= -1;
                }
                $sum += $value;
            }
            return $sum;
    }

    public function calculateCurrentWeekProfit(Carbon $currentDate)
    {

    return 'test';
    }

    public function calculateCurrentYearProfit(Carbon $currentDate)
    {

    return 'test 2';
    }
}
