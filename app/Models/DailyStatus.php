<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyStatus extends Model
{
    protected $fillable = [
        'date',
        'currency',
        'balance',
        'broker_account_id',
    ];
}
