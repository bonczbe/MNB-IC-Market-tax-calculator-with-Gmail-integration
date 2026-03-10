<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyStatus extends Model
{
    protected $fillable = [
        'date',
        'currency',
        'previous_ledger_balance',
        'balance',
    ];

}
