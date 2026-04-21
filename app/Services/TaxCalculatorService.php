<?php

namespace App\Services;

use App\Enums\AccountTransactionTypeEnum;
use App\Repositories\BrokerAccountRepository;
use App\Repositories\DailyStatusRepository;
use App\Repositories\RateRepository;
use App\Repositories\YearlyTaxCalculationRepository;
use Carbon\Carbon;
use Filament\Support\Colors\Color;
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

    public function calculateAllBrokerAccountTaxForYear(Carbon $currentDate, $userId)
    {
        $startOfYear = $currentDate->copy()->startOfYear();
        $endOfYear = $currentDate->copy()->endOfYear();

        ['brokers' => $brokers, 'rates' => $ratesOfTheYear] = $this->getRatesAndBrokersForDateBetween($currentDate, $startOfYear, $endOfYear, $userId);

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

            $previousYear = $broker->yearlyTaxCalculations->first();

            $grossProfit = $allProfitInExchangedCurrency;

            if ($previousYear !== null) {
                $allProfitInExchangedCurrency -= $previousYear->unused_loss;
            }

            $tax = ceil($allProfitInExchangedCurrency * config('tax.volume'));

            $unusedLoss = ($grossProfit < 0 && $allProfitInExchangedCurrency < 0) ? $grossProfit : 0;

            $upsertData = [
                'broker_account_id' => $broker->id,
                'tax_year' => $currentDate->year,
                'gross_profit' => $grossProfit,
                'loss_carried_forward' => $previousYear->unused_loss ?? 0,
                'taxable_income' => $allProfitInExchangedCurrency,
                'tax_amount' => $tax,
                'unused_loss' => $unusedLoss,
            ];

            $this->yearly_tax_calculation_repository
                ->upsert($upsertData, ['broker_account_id', 'tax_year']);
        }
    }

    public function calculateAllBrokerAccountTaxForActualYear(Carbon $currentDate, $userId)
    {
        $startOfYear = $currentDate->copy()->startOfYear();
        $endOfYear = $currentDate->copy()->endOfYear();

        ['brokers' => $brokers, 'rates' => $ratesOfTheYear] = $this->getRatesAndBrokersForDateBetween($currentDate, $startOfYear, $endOfYear, $userId);

        $tax = $this->calculateTotalTaxForBrokers($brokers, $ratesOfTheYear, $startOfYear, config('tax.volume'));

        return number_format(ceil($tax)).' '.config('tax.base_currency');

    }

    public function calculateTaxForPreviousYears(Carbon $currentYear)
    {

        $previousCards = [];

        $previousYears = Cache::remember('previousYears'.auth()->user()->id, Carbon::now()->endOfDay()->subMinute(1), fn () => $this
            ->yearly_tax_calculation_repository
            ->getAllExistingYearsExceptTheGivenDate($currentYear)
        );

        foreach ($previousYears as $prevYear) {
            $yearTax = 0;
            $yearDatas = Cache::remember('yearDatas'.auth()->user()->id.$prevYear, Carbon::now()->endOfDay()->subMinute(1), fn () => $this
                ->yearly_tax_calculation_repository
                ->getByDate($prevYear)
            );

            foreach ($yearDatas as $yd) {
                $yearTax += $yd->tax_amount;
            }

            $formattedResult = number_format(ceil($yearTax)).' '.config('tax.base_currency');

            $previousCards[] =
            Stat::make("{$prevYear} Tax", $formattedResult)
                ->description('Final settled tax')
                ->descriptionIcon('heroicon-m-archive-box-check')
                ->color(Color::Gray);
        }

        return $previousCards;

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

            $depositAndWithdrawSum = $this->sumOfTransactions($transactions);

            $dailyProfitOrLoss = ($previousStatus !== null) ?
                $status->balance - ($previousStatus->balance + $depositAndWithdrawSum) :
                (($lastBeforeTheYear !== null) ?
                    $status->balance - ($lastBeforeTheYear->balance + $depositAndWithdrawSum) :
                    $status->balance - ($starterBalance + $depositAndWithdrawSum)
                );

            $allProfitInExchangedCurrency += $dailyProfitOrLoss * (($rate->rate ?? 1) / ($rate->unit ?? 1));

            $previousStatus = $status;
        }

        return $allProfitInExchangedCurrency;
    }

    private function sumOfTransactions($transactions)
    {
        $sum = 0;
        foreach ($transactions as $transaction) {
            $value = $transaction->amount;
            if ($transaction->type == AccountTransactionTypeEnum::WITHDRAWAL) {
                $value *= -1;
            }
            $sum += $value;
        }

        return $sum;
    }

    public function calculateCurrentWeekNetProfit(Carbon $currentDate, $userId)
    {
        $startOfWeek = $currentDate->copy()->startOfWeek();
        $endOfWeek = $currentDate->copy()->endOfWeek();

        return $this->calculateNetProfitForDatesBetween($startOfWeek, $endOfWeek, $currentDate, $userId);
    }

    public function calculateGrossProfitOfYear(Carbon $currentDate, $userId)
    {
        $startOfYear = $currentDate->copy()->startOfYear();
        $endOfYear = $currentDate->copy()->endOfYear();

        ['brokers' => $brokers, 'rates' => $ratesOfTheYear] = $this->getRatesAndBrokersForDateBetween($currentDate, $startOfYear, $endOfYear, $userId);

        $tax = $this->calculateTotalTaxForBrokers($brokers, $ratesOfTheYear, $startOfYear, 1);

        return number_format(ceil($tax)).' '.config('tax.base_currency');
    }

    public function calculateCurrentYearNetProfit(Carbon $currentDate, $userId)
    {
        $startOfYear = $currentDate->copy()->startOfYear();
        $endOfYear = $currentDate->copy()->endOfYear();

        return $this->calculateNetProfitForDatesBetween($startOfYear, $endOfYear, $currentDate, $userId);

    }

    private function calculateNetProfitForDatesBetween(Carbon $start, Carbon $end, Carbon $currentDate, $userId)
    {
        ['brokers' => $brokers, 'rates' => $rates] = $this->getRatesAndBrokersForDateBetween($currentDate, $start, $end, $userId);

        $tax = $this->calculateTotalTaxForBrokers($brokers, $rates, $start, (1 - config('tax.volume')));

        return number_format(ceil($tax)).' '.config('tax.base_currency');
    }

    private function getRatesAndBrokersForDateBetween(Carbon $current, Carbon $start, Carbon $end, $userId)
    {
        $brokers = $this->broker_account_repository
            ->getAccountsWithYearlyTransactionsStatusesAndTax($current, $start, $end, $userId);

        $rates = $this->rate_repository
            ->getRatesBetweenDates($start, $end);

        return ['brokers' => $brokers, 'rates' => $rates];
    }

    private function calculateTotalTaxForBrokers($brokers, $rates, Carbon $startDate, $taxValue)
    {
        $tax = 0;

        foreach ($brokers as $broker) {
            $allProfitInExchangedCurrency = 0;
            $starterBalance = $broker->starting_balance;

            $ratesForBroker = $rates
                ->filter(fn ($rate) => $rate->base_currency == $broker->broker_currency);

            $lastBeforeStartDate = $this->daily_status_repository
                ->firstSmallerDatedStatus($broker->id, $startDate);

            $allProfitInExchangedCurrency += $this->calculateYearlyProfitInBaseCurrency(
                $broker,
                $ratesForBroker,
                $lastBeforeStartDate,
                $starterBalance);

            $previousYear = $broker->yearlyTaxCalculations->first();

            if ($previousYear !== null) {
                $allProfitInExchangedCurrency -= $previousYear->unused_loss;
            }

            $tax += ceil($allProfitInExchangedCurrency * $taxValue);

        }

        return $tax;
    }
}
