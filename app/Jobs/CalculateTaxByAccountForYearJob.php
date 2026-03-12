<?php

namespace App\Jobs;

use App\Models\BrokerAccount;
use App\Models\DailyStatus;
use App\Models\Rate;
use App\Models\YearlyTaxCalculation;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CalculateTaxByAccountForYearJob implements ShouldQueue
{
    use Queueable;

    public $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $currentYear = Carbon::now();
        $startOfYear = $currentYear->copy()->startOfYear();
        $endOfYear = $currentYear->copy()->endOfYear();

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

            $grossProfit = $allProfitInExchangedCurrency;

            if ($previouseYear !== null) {
                $allProfitInExchangedCurrency -= $previouseYear->unused_loss;
            }

            $tax = ceil($allProfitInExchangedCurrency * 0.15);

            $unusedLoss = ($grossProfit < 0 && $allProfitInExchangedCurrency < 0) ? $grossProfit : 0;

            YearlyTaxCalculation::upsert([
                'broker_account_id' => $broker->id,
                'tax_year' => $currentYear->copy()->format('Y'),
                'gross_profit' => $grossProfit,
                'loss_carried_forward' => $previouseYear->unused_loss ?? 0,
                'taxable_income' => $allProfitInExchangedCurrency,
                'tax_amount' => $tax,
                'unused_loss' => $unusedLoss,

            ], uniqueBy: ['broker_account_id', 'tax_year']);
        }
    }
}
