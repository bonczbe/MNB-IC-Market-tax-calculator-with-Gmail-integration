<?php

namespace App\Services;

use App\Models\BrokerAccount;
use App\Repositories\BrokerAccountRepository;
use App\Repositories\DailyStatusRepository;
use App\Repositories\EmailExtractRepository;
use Carbon\Carbon;
use DirectoryTree\ImapEngine\Mailbox;
use DOMDocument;
use DOMXPath;
use Illuminate\Support\Facades\Cache;

class EmailExtractorService
{
    public function __construct(
        private readonly BrokerAccountRepository $broker_account_repository,
        private readonly DailyStatusRepository $daily_status_repository,
        private readonly EmailExtractRepository $email_extract_repository) {}

    public function extratAndSaveEmail()
    {
        $mailbox = new Mailbox(config('imap.default'));

        $accounts = $this->broker_account_repository
            ->getAll();

        foreach ($accounts as $account) {

            $inbox = $mailbox->inbox();

            $messages = $inbox->messages()
                ->since(Carbon::now()->subDays(1))
                ->before(today()->addDay())
                ->from($account->email)
                ->subject($account->email_subject)
                ->withBody()
                ->withBodyStructure()
                ->get();

            $emails = [];
            $dailyStatuses = [];

            foreach ($messages as $message) {
                $extracted = $this->extractMessage($message, $account);

                if ($extracted['email']) {
                    $emails[] = $extracted['email'];
                }

                if ($extracted['dailyStatus']) {
                    $dailyStatuses[] = $extracted['dailyStatus'];
                }
            }

            $this->email_extract_repository->chunkedUpsert($emails, uniqueBy: ['date', 'broker_account_id']);

            $this->daily_status_repository->chunkedUpsert($dailyStatuses, uniqueBy: ['date', 'broker_account_id']);

            Cache::forget('calculateCurrentYear');
        }
    }

    private function extractMessage($message, BrokerAccount $account)
    {
        $raw = $message->bodyPart('1');
        $html = base64_decode($raw);

        $dom = new DOMDocument;
        @$dom->loadHTML($html);
        $xpath = new DOMXPath($dom);
        $email = null;
        $dailyStatus = null;

        $filterNumber = $xpath->query($account->filter_number);

        if ($filterNumber->length > 0) {
            $date = Carbon::now()
                ->format('Y-m-d');

            $email = [
                'date' => $date,
                'content' => $html,
                'broker_account_id' => $account->id,
            ];

            $query = $account->filter_balance;
            $nodeList = $xpath->query($query);
            $balance = (float) str_replace(' ', '', $nodeList->item(0)->nodeValue);

            $dailyStatus = [
                'date' => $date,
                'balance' => $balance,
                'currency' => $account->broker_currency,
                'broker_account_id' => $account->id,
            ];

        }

        return ['email' => $email, 'dailyStatus' => $dailyStatus];
    }
}
