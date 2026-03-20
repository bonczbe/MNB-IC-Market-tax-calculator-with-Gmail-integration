<?php

namespace App\Listeners;

use Filament\Notifications\Notification;
use Illuminate\Auth\Events\Login;

class ShowAccountNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        $user = $event->user;

        if ($user->imap_username == 'change-me@change.me') {
            Notification::make()
                ->title('Need to update your Imap settings!')
                ->body('Open your user settings on Users view and modify them!')
                ->warning()
                ->send();
        }
    }
}
