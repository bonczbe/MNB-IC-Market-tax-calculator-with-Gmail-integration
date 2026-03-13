<?php

namespace App\Repositories;

use App\Models\YearlyTaxCalculation;
use Carbon\Carbon;

class YearlyTaxCalculationRepository
{
    public function __construct() {}

    public function upsert(array $data, array $uniqueBy)
    {
        return YearlyTaxCalculation::upsert($data, uniqueBy: $uniqueBy);
    }

    public function getAllExitingYearsExepctTheGivenDate(Carbon $date){
        return YearlyTaxCalculation::query()
            ->where('tax_year', '<>', $date->copy()->format('Y'))
            ->orderBy('tax_year', 'desc')
            ->distinct()
            ->pluck('tax_year');
    }

    public function getByDate(Carbon $date){
        return YearlyTaxCalculation::query()
                ->where('tax_year', $date)
                ->with('broker')
                ->get();
    }
}
