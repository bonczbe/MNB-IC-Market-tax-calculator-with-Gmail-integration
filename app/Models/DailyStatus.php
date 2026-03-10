<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyStatus extends Model
{
    protected $fillable = [
        'date',
        'currency',
        'balance',
        'broker_account_id',
    ];

    public function broker(): BelongsTo
    {
        return $this->belongsTo(BrokerAccount::class, 'broker_account_id');
    }
}
