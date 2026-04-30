<?php

namespace App\Models;

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

    protected static function booted(): void
    {

        static::updated(function (DailyStatus $dailyStatus) {
            Cache::forget('DailyStatusCalculated_'.$dailyStatus->id);
            Cache::forget('weekly_chart_data'.(auth()->user()->id??0).'_'.$dailyStatus->broker->id);
        });
    }
}
