<?php

namespace App\Providers;

use Carbon\CarbonImmutable;
use DOMDocument;
use DOMXPath;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
/*
$html = file_get_contents('https://www.mnb.hu/en/arfolyamok'); // vagy $html = $response;

$dom = new DOMDocument();
@$dom->loadHTML($html);
$xpath = new DOMXPath($dom);

// Megkeresi azt a sort ahol a td szövege "EUR"
$rows = $xpath->query('//tr[td[@class="fw-b" and text()="EUR"]]');

$eurRate = null;
foreach ($rows as $row) {
    $cells = $row->getElementsByTagName('td');
    // 0: kód (EUR), 1: név (Euro), 2: egység (1), 3: érték (391.29)
    if ($cells->length >= 4) {
        $eurRate = trim($cells->item(3)->nodeValue);
    }
}

echo json_encode([
    'currency' => 'EUR',
    'name'     => 'Euro',
    'unit'     => 1,
    'rate_huf' => (float) $eurRate,
    'date'     => '2026-03-06',
]);


*/
