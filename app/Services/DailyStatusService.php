<?php

namespace App\Services;

use App\Enums\AccountTransactionTypeEnum;
use App\Models\DailyStatus;
use App\Repositories\DailyStatusRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class DailyStatusService
{
    public function __construct(private readonly DailyStatusRepository $daily_status_repository) {}

    public function calculateProfitForDay(DailyStatus $record)
    {
        return Cache::remember('DailyStatusCalculated_'.$record->id, Carbon::now()->addMinutes(30), function () use ($record) {

            $prevBalance = $this
                    ->firstSmallerDatedStatus($record->broker->id, Carbon::parse($record->date));

            $transactions = $record->broker->accountTransactions->filter(fn ($act) => $act->date == $record->date);

            $depositAndWithdrawSum = $this->sumOfTransactions($transactions);

            if ($prevBalance == null) {
                return $record->balance - $record->broker->starting_balance - $depositAndWithdrawSum;
            }

            return $record->balance - $prevBalance->balance - $depositAndWithdrawSum;
        });
    }

    public function sumOfTransactions(iterable $transactions)
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

    public function firstSmallerDatedStatus(int $brokerId,Carbon $startDate)
    {
        return $this->daily_status_repository
                ->firstSmallerDatedStatus($brokerId, $startDate);;
    }
}
