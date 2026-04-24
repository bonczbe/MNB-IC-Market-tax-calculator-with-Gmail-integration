<?php

namespace Tests\Feature;

use App\Enums\AccountTransactionTypeEnum;
use App\Models\AccountTransaction;
use App\Models\BrokerAccount;
use App\Models\DailyStatus;
use App\Models\Rate;
use App\Models\User;
use App\Models\YearlyTaxCalculation;
use App\Services\TaxCalculatorService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaxCalculatorServiceTest extends TestCase
{
    use RefreshDatabase;

    private TaxCalculatorService $service;

    private User $user;

    private BrokerAccount $broker;

    protected function setUp(): void
    {
        parent::setUp();

        config(['tax.volume' => 0.15, 'tax.base_currency' => 'HUF']);

        $this->service = app(TaxCalculatorService::class);
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
        $this->broker = BrokerAccount::factory()->create([
            'user_id' => $this->user->id,
            'broker_currency' => 'USD',
            'starting_balance' => 1000.00,
        ]);
    }

    public function test_calculate_gross_profit_of_year(): void
    {
        DailyStatus::factory()->create([
            'broker_account_id' => $this->broker->id,
            'date' => '2024-06-15',
            'balance' => 1200.00,
        ]);

        Rate::factory()->create([
            'base_currency' => 'USD',
            'for_currency' => 'HUF',
            'date' => '2024-06-15',
            'rate' => 370.00,
            'unit' => 1,
        ]);

        // (1200 - 1000) * 370 = 74000
        $result = $this->service->calculateGrossProfitOfYear(Carbon::create(2024, 6, 15), $this->user->id);

        $this->assertStringContainsString('74,000', $result);
        $this->assertStringEndsWith('HUF', $result);
    }

    public function test_calculate_gross_profit_subtracts_deposit(): void
    {
        DailyStatus::factory()->create([
            'broker_account_id' => $this->broker->id,
            'date' => '2024-06-15',
            'balance' => 1500.00,
        ]);

        AccountTransaction::factory()->create([
            'broker_account_id' => $this->broker->id,
            'date' => '2024-06-15',
            'amount' => 200.00,
            'type' => AccountTransactionTypeEnum::DEPOSIT,
        ]);

        Rate::factory()->create([
            'base_currency' => 'USD',
            'for_currency' => 'HUF',
            'date' => '2024-06-15',
            'rate' => 370.00,
            'unit' => 1,
        ]);

        // (1500 - (1000 + 200)) * 370 = 111000
        $result = $this->service->calculateGrossProfitOfYear(Carbon::create(2024, 6, 15), $this->user->id);

        $this->assertStringContainsString('111,000', $result);
    }

    public function test_calculate_gross_profit_adds_back_withdrawal(): void
    {
        DailyStatus::factory()->create([
            'broker_account_id' => $this->broker->id,
            'date' => '2024-06-15',
            'currency' => 'USD',
            'balance' => 900.00,
        ]);

        AccountTransaction::factory()->create([
            'broker_account_id' => $this->broker->id,
            'date' => '2024-06-15',
            'amount' => 200.00,
            'type' => AccountTransactionTypeEnum::WITHDRAWAL,
        ]);

        Rate::factory()->create([
            'base_currency' => 'USD',
            'for_currency' => 'HUF',
            'date' => '2024-06-15',
            'rate' => 370.00,
            'unit' => 1,
        ]);

        // (900 - (1000 - 200)) * 370 = 37000
        $result = $this->service->calculateGrossProfitOfYear(Carbon::create(2024, 6, 15), $this->user->id);

        $this->assertStringContainsString('37,000', $result);
    }

    public function test_calculate_current_year_net_profit_applies_tax(): void
    {
        DailyStatus::factory()->create([
            'broker_account_id' => $this->broker->id,
            'date' => '2024-06-15',
            'balance' => 1200.00,
        ]);

        Rate::factory()->create([
            'base_currency' => 'USD',
            'for_currency' => 'HUF',
            'date' => '2024-06-15',
            'rate' => 370.00,
            'unit' => 1,
        ]);

        // 74000 * (1 - 0.15) = 62900
        $result = $this->service->calculateCurrentYearNetProfit(Carbon::create(2024, 6, 15), $this->user->id);

        $this->assertStringContainsString('62,900', $result);
        $this->assertStringEndsWith('HUF', $result);
    }

    public function test_calculate_all_broker_account_tax_for_actual_year(): void
    {
        DailyStatus::factory()->create([
            'broker_account_id' => $this->broker->id,
            'date' => '2024-06-15',
            'balance' => 1200.00,
        ]);

        Rate::factory()->create([
            'base_currency' => 'USD',
            'for_currency' => 'HUF',
            'date' => '2024-06-15',
            'rate' => 370.00,
            'unit' => 1,
        ]);

        // 74000 * 0.15 = 11100
        $result = $this->service->calculateAllBrokerAccountTaxForActualYear(Carbon::create(2024, 6, 15), $this->user->id);

        $this->assertStringContainsString('11,100', $result);
        $this->assertStringEndsWith('HUF', $result);
    }

    public function test_calculate_all_broker_account_tax_for_year_upserts_to_db(): void
    {
        DailyStatus::factory()->create([
            'broker_account_id' => $this->broker->id,
            'date' => '2024-06-15',
            'balance' => 1200.00,
        ]);

        Rate::factory()->create([
            'base_currency' => 'USD',
            'for_currency' => 'HUF',
            'date' => '2024-06-15',
            'rate' => 370.00,
            'unit' => 1,
        ]);

        $this->service->calculateAllBrokerAccountTaxForYear(Carbon::create(2024, 6, 15), $this->user->id);

        $this->assertDatabaseHas('yearly_tax_calculations', [
            'broker_account_id' => $this->broker->id,
            'tax_year' => 2024,
            'gross_profit' => 74000,
            'tax_amount' => (int) ceil(74000 * 0.15),
            'unused_loss' => 0,
        ]);
    }

    public function test_calculate_all_broker_account_tax_for_year_does_not_duplicate(): void
    {
        DailyStatus::factory()->create([
            'broker_account_id' => $this->broker->id,
            'date' => '2024-06-15',
            'balance' => 1200.00,
        ]);

        Rate::factory()->create([
            'base_currency' => 'USD',
            'for_currency' => 'HUF',
            'date' => '2024-06-15',
            'rate' => 370.00,
            'unit' => 1,
        ]);

        $this->service->calculateAllBrokerAccountTaxForYear(Carbon::create(2024, 6, 15), $this->user->id);
        $this->service->calculateAllBrokerAccountTaxForYear(Carbon::create(2024, 6, 15), $this->user->id);

        $this->assertDatabaseCount('yearly_tax_calculations', 1);
    }

    public function test_calculate_all_broker_account_tax_for_year_carries_forward_loss(): void
    {
        YearlyTaxCalculation::factory()->create([
            'broker_account_id' => $this->broker->id,
            'tax_year' => 2023,
            'unused_loss' => -10000,
        ]);

        DailyStatus::factory()->create([
            'broker_account_id' => $this->broker->id,
            'date' => '2024-06-15',
            'balance' => 1200.00,
        ]);

        Rate::factory()->create([
            'base_currency' => 'USD',
            'for_currency' => 'HUF',
            'date' => '2024-06-15',
            'rate' => 370.00,
            'unit' => 1,
        ]);

        $this->service->calculateAllBrokerAccountTaxForYear(Carbon::create(2024, 6, 15), $this->user->id);

        $record = YearlyTaxCalculation::where('broker_account_id', $this->broker->id)
            ->where('tax_year', 2024)
            ->first();

        $this->assertEquals(-10000, $record->loss_carried_forward);
    }

    public function test_calculate_all_broker_account_tax_for_year_stores_unused_loss(): void
    {
        DailyStatus::factory()->create([
            'broker_account_id' => $this->broker->id,
            'date' => '2024-06-15',
            'balance' => 800.00,
        ]);

        Rate::factory()->create([
            'base_currency' => 'USD',
            'for_currency' => 'HUF',
            'date' => '2024-06-15',
            'rate' => 370.00,
            'unit' => 1,
        ]);

        // (800 - 1000) * 370 = -74000
        $this->service->calculateAllBrokerAccountTaxForYear(Carbon::create(2024, 6, 15), $this->user->id);

        $this->assertDatabaseHas('yearly_tax_calculations', [
            'broker_account_id' => $this->broker->id,
            'unused_loss' => -74000,
        ]);
    }

    public function test_calculate_all_broker_account_tax_for_year_zero_tax_when_negative_profit(): void
    {
        DailyStatus::factory()->create([
            'broker_account_id' => $this->broker->id,
            'date' => '2024-06-15',
            'balance' => 500.00,
        ]);

        Rate::factory()->create([
            'base_currency' => 'USD',
            'for_currency' => 'HUF',
            'date' => '2024-06-15',
            'rate' => 370.00,
            'unit' => 1,
        ]);

        $this->service->calculateAllBrokerAccountTaxForYear(Carbon::create(2024, 6, 15), $this->user->id);

        $record = YearlyTaxCalculation::where('broker_account_id', $this->broker->id)->first();

        $this->assertLessThanOrEqual(0, $record->tax_amount);
    }

    public function test_calculate_gross_profit_accumulates_multiple_daily_statuses(): void
    {
        DailyStatus::factory()->create([
            'broker_account_id' => $this->broker->id,
            'date' => '2024-01-15',
            'balance' => 1100.00,
        ]);

        DailyStatus::factory()->create([
            'broker_account_id' => $this->broker->id,
            'date' => '2024-02-15',
            'balance' => 1300.00,
        ]);

        Rate::factory()->create([
            'base_currency' => 'USD',
            'for_currency' => 'HUF',
            'date' => '2024-01-15',
            'rate' => 370.00,
            'unit' => 1,
        ]);

        Rate::factory()->create([
            'base_currency' => 'USD',
            'for_currency' => 'HUF',
            'date' => '2024-02-15',
            'rate' => 380.00,
            'unit' => 1,
        ]);

        // day1: (1100-1000)*370=37000, day2: (1300-1100)*380=76000, total: 113000
        $result = $this->service->calculateGrossProfitOfYear(Carbon::create(2024, 3, 1), $this->user->id);

        $this->assertStringContainsString('113,000', $result);
    }

    public function test_calculate_gross_profit_uses_previous_day_balance_as_base(): void
    {
        DailyStatus::factory()->create([
            'broker_account_id' => $this->broker->id,
            'date' => '2024-01-10',
            'balance' => 1050.00,
        ]);

        DailyStatus::factory()->create([
            'broker_account_id' => $this->broker->id,
            'date' => '2024-01-11',
            'balance' => 1100.00,
        ]);

        Rate::factory()->create([
            'base_currency' => 'USD',
            'for_currency' => 'HUF',
            'date' => '2024-01-10',
            'rate' => 370.00,
            'unit' => 1,
        ]);

        Rate::factory()->create([
            'base_currency' => 'USD',
            'for_currency' => 'HUF',
            'date' => '2024-01-11',
            'rate' => 370.00,
            'unit' => 1,
        ]);

        // day1: (1050-1000)*370=18500, day2: (1100-1050)*370=18500, total: 37000
        $result = $this->service->calculateGrossProfitOfYear(Carbon::create(2024, 2, 1), $this->user->id);

        $this->assertStringContainsString('37,000', $result);
    }

    public function test_calculate_gross_profit_fallback_rate_is_1_when_no_rate_found(): void
    {
        DailyStatus::factory()->create([
            'broker_account_id' => $this->broker->id,
            'date' => '2024-06-15',
            'balance' => 1200.00,
        ]);

        // no rate inserted → (1200 - 1000) * 1 = 200
        $result = $this->service->calculateGrossProfitOfYear(Carbon::create(2024, 6, 15), $this->user->id);

        $this->assertStringContainsString('200', $result);
    }

    public function test_calculate_all_broker_account_tax_for_year_creates_record_per_broker(): void
    {
        $broker2 = BrokerAccount::factory()->create([
            'user_id' => $this->user->id,
            'broker_currency' => 'USD',
            'starting_balance' => 2000.00,
        ]);

        foreach ([$this->broker->id, $broker2->id] as $brokerId) {
            DailyStatus::factory()->create([
                'broker_account_id' => $brokerId,
                'date' => '2024-06-15',
                'balance' => 2500.00,
            ]);
        }

        Rate::factory()->create([
            'base_currency' => 'USD',
            'for_currency' => 'HUF',
            'date' => '2024-06-15',
            'rate' => 370.00,
            'unit' => 1,
        ]);

        $this->service->calculateAllBrokerAccountTaxForYear(Carbon::create(2024, 6, 15), $this->user->id);

        $this->assertDatabaseCount('yearly_tax_calculations', 2);
    }
}
