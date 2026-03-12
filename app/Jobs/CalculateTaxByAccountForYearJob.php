<?php

namespace App\Jobs;

use App\Services\TaxCalculatorService;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CalculateTaxByAccountForYearJob implements ShouldQueue
{
    use Queueable;

    public $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(
    ) {}

    /**
     * Execute the job.
     */
    public function handle(TaxCalculatorService $tax_calculator_service): void
    {
        $currentYear = Carbon::now();

        $tax_calculator_service->calculateAllBrokerAccountTaxForYear($currentYear);

    }
}
