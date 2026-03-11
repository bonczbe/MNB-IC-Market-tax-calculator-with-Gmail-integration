<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class YearlyTaxCalculation extends Model
{
    protected $fillable = [
        'broker_account_id',
        'tax_year',
        'gross_profit',
        'loss_carried_forward',
        'taxable_income',
        'tax_amount',
        'unused_loss',
    ];

    public function broker(): BelongsTo
    {
        return $this->belongsTo(BrokerAccount::class, 'broker_account_id');
    }
}
