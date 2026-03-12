<?php

namespace App\Services;

use App\Repositories\BrokerAccountRepository;
use App\Repositories\DailyStatusRepository;
use App\Repositories\RateRepository;
use App\Repositories\YearlyTaxCalculationRepository;
use Carbon\Carbon;

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
            $allProfitInOriginalCurrancy = 0;
            $starterBalance = $broker->starting_balance;

            $ratesForBroker = $ratesOfTheYear
                ->filter(fn ($rate) => $rate->base_currency == $broker->broker_currency);

            $lastBeforeTheYear = $this->daily_status_repository
                ->firstSallerDatedStatus($broker->id, $startOfYear);

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

            $grossProfit = $allProfitInExchangedCurrency;

            if ($previouseYear !== null) {
                $allProfitInExchangedCurrency -= $previouseYear->unused_loss;
            }

            $tax = ceil($allProfitInExchangedCurrency * env('TAX_VOLUME', 0));

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
}
