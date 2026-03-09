<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BrokerAccount extends Model
{
    //
    protected $fillable = [
        'broker_name',
        'account_number',
    ];
}
