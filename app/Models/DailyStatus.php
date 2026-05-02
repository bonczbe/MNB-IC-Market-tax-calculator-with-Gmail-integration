<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

class DailyStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'currency',
        'balance',
        'broker_account_id',
    ];

    protected $casts = [
        'date' => 'date',
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

        static::created(function (DailyStatus $dailyStatus) {
            $dailyStatus->resetCaches();
        });

        static::updated(function (DailyStatus $dailyStatus) {
            $dailyStatus->resetCaches();
        });
    }
}
