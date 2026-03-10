<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BrokerAccount extends Model
{
    //
    protected $fillable = [
        'broker_name',
        'email',
        'email_subject',
        'account_number',
        'starting_balance',
        'filter_number',
        'filter_balance',
        'broker_currency',
    ];
}
