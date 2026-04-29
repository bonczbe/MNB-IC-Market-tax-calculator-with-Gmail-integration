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
            $depositAndWithdrawSum = 0;

            $prevBalance = Cache::remember(
                'DailyStatus'.$record->broker->user->id.'$'.$record->date.'$'.$record->broker->id,
                86400,
                fn () => $this->daily_status_repository
                    ->firstSmallerDatedStatus($record->broker->id, Carbon::parse($record->date))
            );

            $transactions = $record->broker->accountTransactions->filter(fn ($act) => $act->date == $record->date);

            foreach ($transactions as $transaction) {
                $value = $transaction->amount;
                if ($transaction->type == AccountTransactionTypeEnum::WITHDRAWAL) {
                    $value *= -1;
                }
                $depositAndWithdrawSum += $value;
            }

            if ($prevBalance == null) {
                return $record->balance - $record->broker->starting_balance - $depositAndWithdrawSum;
            }

            return $record->balance - $prevBalance->balance - $depositAndWithdrawSum;
        });
    }
}
