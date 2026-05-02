<?php

namespace App\Models;

use App\Enums\AccountTransactionTypeEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

class AccountTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'broker_account_id',
        'date',
        'type',
        'amount',
        'note',
    ];

    protected $casts = [
        'date' => 'date',
        'type' => AccountTransactionTypeEnum::class,
    ];

    public function broker(): BelongsTo
    {
        return $this->belongsTo(BrokerAccount::class, 'broker_account_id');
    }

    public function resetCaches()
    {
        $currentDate = Carbon::now();

        Cache::forget('DailyStatusCalculated_'.$this->id);

        $userId = $this->broker()->value('user_id');
        $brokerId = $this->broker_account_id;

        if ($userId !== null) {
            Cache::forget("yearly_chart_data_{$userId}_current_year_0");
            Cache::forget("yearly_chart_data_{$userId}_current_year_{$brokerId}");
            Cache::forget("weekly_chart_data{$userId}_{$brokerId}");
            Cache::forget("weekly_chart_data{$userId}_0");
            Cache::forget("calculatecurrentDate{$userId}");
            Cache::forget("profitForTheWeek{$userId}w_{$currentDate->copy()->format('W')}");
            Cache::forget("grossProfitOfYear{$userId}");
            Cache::forget("profitForYear{$userId}");
        }
    }

    protected static function booted(): void
    {

        static::created(function (AccountTransaction $trans) {
            $trans->resetCaches();
        });

        static::updated(function (AccountTransaction $trans) {
            $trans->resetCaches();
        });
    }
}
