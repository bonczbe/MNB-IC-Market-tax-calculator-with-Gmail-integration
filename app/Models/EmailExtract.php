<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailExtract extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'content',
        'broker_account_id',
    ];

    public function broker(): BelongsTo
    {
        return $this->belongsTo(BrokerAccount::class, 'broker_account_id');
    }
}
