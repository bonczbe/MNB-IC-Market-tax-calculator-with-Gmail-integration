<?php

namespace App\Repositories;

use App\Models\YearlyTaxCalculation;

class YearlyTaxCalculationRepository
{
    public function __construct() {}

    public function upsert(array $data, array $uniqueBy)
    {
        return YearlyTaxCalculation::upsert([
            'broker_account_id' => $data['brokerId'],
            'tax_year' => $data['taxYear'],
            'gross_profit' => $data['grossProfit'],
            'loss_carried_forward' => $data['lossCarried'],
            'taxable_income' => $data['taxableIncome'],
            'tax_amount' => $data['taxAmount'],
            'unused_loss' => $data['remainingLoss'],

        ], uniqueBy: $uniqueBy);
    }
}
