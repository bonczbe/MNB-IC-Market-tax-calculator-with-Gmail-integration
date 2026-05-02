<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class BrokerAccount extends Model
{
    use HasFactory;

    //
    protected $fillable = [
        'broker_name',
        'email',
        'email_subject',
        'account_number',
        'starting_balance',
        'filter_number',
        'filter_balance',
        'filter_date',
        'broker_currency',
        'user_id',
    ];

    public function dailyStatuses(): HasMany
    {
        return $this->hasMany(DailyStatus::class, 'broker_account_id');
    }

    public function emailExtracts(): HasMany
    {
        return $this->hasMany(EmailExtract::class, 'broker_account_id');
    }

    public function accountTransactions(): HasMany
    {
        return $this->hasMany(AccountTransaction::class, 'broker_account_id');
    }

    public function yearlyTaxCalculations(): HasMany
    {
        return $this->hasMany(YearlyTaxCalculation::class, 'broker_account_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function resetCaches()
    {
        $currentDate = Carbon::now();

        Cache::forget('DailyStatusCalculated_'.$this->id);

        $userId = $this->user_id;
        $brokerId = $this->id;

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

        static::created(function (BrokerAccount $broker) {
            $broker->resetCaches();
        });

        static::updated(function (BrokerAccount $broker) {
            $broker->resetCaches();
        });
    }
}
