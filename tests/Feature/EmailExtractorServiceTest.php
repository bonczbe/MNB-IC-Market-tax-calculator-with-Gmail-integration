<?php

namespace Tests\Feature\Services;

use App\Models\BrokerAccount;
use App\Models\User;
use App\Services\EmailExtractorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Mockery;
use Tests\TestCase;

class EmailExtractorServiceTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private BrokerAccount $broker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->broker = BrokerAccount::factory()->create([
            'user_id' => $this->user->id,
            'broker_currency' => 'USD',
            'starting_balance' => 1000.00,
            'filter_number' => '//td[@class="account-number"]',
            'filter_balance' => '//td[@class="balance"]',
        ]);
    }

    private function callExtractMessage(object $message, BrokerAccount $account): array
    {
        $service = app(EmailExtractorService::class);
        $reflection = new \ReflectionMethod($service, 'extractMessage');
        $reflection->setAccessible(true);

        return $reflection->invokeArgs($service, [$message, $account]);
    }

    private function fakeMessage(string $html): object
    {
        $message = Mockery::mock();
        $message->shouldReceive('bodyPart')->andReturn(base64_encode($html));

        return $message;
    }

    public function test_extract_message_returns_daily_status_when_filter_matches(): void
    {
        $html = '<html><body>
            <table>
                <tr><td class="account-number">12345678</td></tr>
                <tr><td class="balance">1 234.56</td></tr>
            </table>
        </body></html>';

        $result = $this->callExtractMessage($this->fakeMessage($html), $this->broker);

        $this->assertNotNull($result['dailyStatus']);
        $this->assertEquals(1234.56, $result['dailyStatus']['balance']);
        $this->assertEquals('USD', $result['dailyStatus']['currency']);
        $this->assertEquals($this->broker->id, $result['dailyStatus']['broker_account_id']);
    }

    public function test_extract_message_returns_null_when_filter_not_matched(): void
    {
        $html = '<html><body><p>No relevant content</p></body></html>';

        $result = $this->callExtractMessage($this->fakeMessage($html), $this->broker);

        $this->assertNull($result['email']);
        $this->assertNull($result['dailyStatus']);
    }

    public function test_extract_message_parses_balance_with_spaces(): void
    {
        $html = '<html><body>
            <table>
                <tr><td class="account-number">12345678</td></tr>
                <tr><td class="balance">12 345.67</td></tr>
            </table>
        </body></html>';

        $result = $this->callExtractMessage($this->fakeMessage($html), $this->broker);

        $this->assertEquals(12345.67, $result['dailyStatus']['balance']);
    }

    public function test_extract_message_sets_todays_date(): void
    {
        $html = '<html><body>
            <table>
                <tr><td class="account-number">12345678</td></tr>
                <tr><td class="balance">1000.00</td></tr>
            </table>
        </body></html>';

        $result = $this->callExtractMessage($this->fakeMessage($html), $this->broker);

        $this->assertEquals(now()->format('Y-m-d'), $result['dailyStatus']['date']);
    }

    public function test_extract_message_saves_html_content_in_email(): void
    {
        $html = '<html><body>
            <table>
                <tr><td class="account-number">12345678</td></tr>
                <tr><td class="balance">1000.00</td></tr>
            </table>
        </body></html>';

        $result = $this->callExtractMessage($this->fakeMessage($html), $this->broker);

        $this->assertNotNull($result['email']['content']);
        $this->assertStringContainsString('account-number', $result['email']['content']);
    }

    public function test_cache_is_cleared_for_all_users(): void
    {
        $user2 = User::factory()->create();

        Cache::put('grossProfitOfYear'.$this->user->id, '50,000 HUF', 3600);
        Cache::put('grossProfitOfYear'.$user2->id, '30,000 HUF', 3600);

        Cache::forget('grossProfitOfYear'.$this->user->id);
        Cache::forget('grossProfitOfYear'.$user2->id);

        $this->assertFalse(Cache::has('grossProfitOfYear'.$this->user->id));
        $this->assertFalse(Cache::has('grossProfitOfYear'.$user2->id));
    }
}
