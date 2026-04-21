<?php

namespace App\Models;

use App\Enums\AccountTransactionTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
}
