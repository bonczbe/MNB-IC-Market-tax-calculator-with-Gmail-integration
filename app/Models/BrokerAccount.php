<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'previous_year_minus',
    ];

    public function dailyStatuses(): HasMany
    {
        return $this->hasMany(DailyStatus::class, 'broker_account_id');
    }

    public function emailExtracts(): HasMany
    {
        return $this->hasMany(EmailExtract::class, 'broker_account_id');
    }
}
