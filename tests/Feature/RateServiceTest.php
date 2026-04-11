<?php

namespace Tests\Feature;

use App\Services\RateService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RateServiceTest extends TestCase
{
    use RefreshDatabase;

    private RateService $service;

    protected function setUp(): void
    {
        parent::setUp();

        config(['tax.base_currency' => 'HUF']);

        $this->service = app(RateService::class);
    }

    protected function tearDown(): void
    {
        unset($GLOBALS['__test_file_get_contents']);

        parent::tearDown();
    }

    private function fakeMnbHtml(string $date, array $rows): string
    {
        $rowsHtml = '';
        foreach ($rows as [$currency, $unit, $rate]) {
            $rowsHtml .= "<tr><td class=\"fw-b\">{$currency}</td><td></td><td>{$unit}</td><td>{$rate}</td></tr>";
        }

        return "<html><body>
            <caption class=\"ttl ttl-s\">Exchange rates: {$date}</caption>
            <table>{$rowsHtml}</table>
        </body></html>";
    }

    private function mockHtml(string $html): void
    {
        $GLOBALS['__test_file_get_contents']['https://www.mnb.hu/en/arfolyamok'] = $html;
    }

    public function test_fetch_and_upsert_saves_rates_to_database(): void
    {
        $this->mockHtml($this->fakeMnbHtml('15 March 2026', [
            ['USD', 1, '370.50'],
            ['EUR', 1, '395.20'],
        ]));

        $this->service->fetchAndUpsertRatesByMNB();

        $this->assertDatabaseHas('rates', [
            'base_currency' => 'USD',
            'for_currency' => 'HUF',
            'date' => '2026-03-15',
            'rate' => 370.50,
        ]);

        $this->assertDatabaseHas('rates', [
            'base_currency' => 'EUR',
            'for_currency' => 'HUF',
            'date' => '2026-03-15',
            'rate' => 395.20,
        ]);
    }

    public function test_fetch_and_upsert_does_not_duplicate_records(): void
    {
        $this->mockHtml($this->fakeMnbHtml('15 March 2026', [['USD', 1, '370.50']]));

        $this->service->fetchAndUpsertRatesByMNB();
        $this->service->fetchAndUpsertRatesByMNB();

        $this->assertDatabaseCount('rates', 1);
    }

    public function test_fetch_and_upsert_updates_existing_rate(): void
    {
        $this->mockHtml($this->fakeMnbHtml('15 March 2026', [['USD', 1, '370.50']]));
        $this->service->fetchAndUpsertRatesByMNB();

        $this->mockHtml($this->fakeMnbHtml('15 March 2026', [['USD', 1, '375.00']]));
        $this->service->fetchAndUpsertRatesByMNB();

        $this->assertDatabaseCount('rates', 1);
        $this->assertDatabaseHas('rates', ['base_currency' => 'USD', 'rate' => 375.00]);
    }

    public function test_fetch_and_upsert_parses_date_correctly(): void
    {
        $this->mockHtml($this->fakeMnbHtml('01 January 2025', [['USD', 1, '390.00']]));

        $this->service->fetchAndUpsertRatesByMNB();

        $this->assertDatabaseHas('rates', [
            'base_currency' => 'USD',
            'date' => '2025-01-01',
        ]);
    }

    public function test_fetch_and_upsert_skips_rows_with_less_than_4_cells(): void
    {
        $html = '<html><body>
            <caption class="ttl ttl-s">Exchange rates: 15 March 2026</caption>
            <table>
                <tr><td class="fw-b">USD</td><td></td><td>1</td><td>370.50</td></tr>
                <tr><td class="fw-b">BROKEN</td><td></td></tr>
            </table>
        </body></html>';

        $this->mockHtml($html);
        $this->service->fetchAndUpsertRatesByMNB();

        $this->assertDatabaseCount('rates', 1);
        $this->assertDatabaseHas('rates', ['base_currency' => 'USD']);
    }

    public function test_fetch_and_upsert_trims_whitespace_from_values(): void
    {
        $html = '<html><body>
            <caption class="ttl ttl-s">Exchange rates: 15 March 2026</caption>
            <table>
                <tr><td class="fw-b">  USD  </td><td></td><td>  1  </td><td>  370.50  </td></tr>
            </table>
        </body></html>';

        $this->mockHtml($html);
        $this->service->fetchAndUpsertRatesByMNB();

        $this->assertDatabaseHas('rates', ['base_currency' => 'USD', 'rate' => 370.50]);
    }

    public function test_fetch_and_upsert_sets_for_currency_from_config(): void
    {
        $this->mockHtml($this->fakeMnbHtml('15 March 2026', [['USD', 1, '370.50']]));

        $this->service->fetchAndUpsertRatesByMNB();

        $this->assertDatabaseHas('rates', ['for_currency' => 'HUF']);
    }
}
