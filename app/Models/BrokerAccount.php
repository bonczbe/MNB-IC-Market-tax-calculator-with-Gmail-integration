<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
}
