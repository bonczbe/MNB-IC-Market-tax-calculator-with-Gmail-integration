<?php

namespace App\Jobs;

use App\Models\BrokerAccount;
use App\Models\DailyStatus;
use App\Models\EmailExtract;
use Carbon\Carbon;
use DirectoryTree\ImapEngine\Mailbox;
use DOMDocument;
use DOMXPath;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class BrokerEmailExtractor implements ShouldQueue
{
    use Queueable;

    public $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        $mailbox = new Mailbox(config('imap.default'));

        $accounts = BrokerAccount::query()->get();

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
                $raw = $message->bodyPart('1');
                $html = base64_decode($raw);

                $dom = new DOMDocument;
                @$dom->loadHTML($html);
                $xpath = new DOMXPath($dom);

                $filterNumber = $xpath->query($account->filter_number);

                if ($filterNumber->length > 0) {
                    $date = Carbon::now()->subDays(1)->format('Y-m-d');
                    $emails[] = [
                        'date' => $date,
                        'content' => $html,
                        'broker_account_id' => $account->id,
                    ];

                    $query = $account->filter_balance;
                    $nodeList = $xpath->query($query);
                    $balance = (float) str_replace(' ', '', $nodeList->item(0)->nodeValue);

                    $dailyStatuses[] = [
                        'date' => $date,
                        'balance' => $balance,
                        'currency' => $account->broker_currency,
                        'broker_account_id' => $account->id,
                    ];

                }

            }
            EmailExtract::upsert($emails, uniqueBy: ['date', 'broker_account_id']);
            DailyStatus::upsert($dailyStatuses, uniqueBy: ['date', 'broker_account_id']);

        }
    }
}
