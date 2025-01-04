<?php

namespace App\Listeners;

use App\Events\UserCreatedEvent;

class SendEmailVerificationNotification
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
    public function handle(UserCreatedEvent $event): void
    {
        $event->user->sendEmailVerificationNotification();
    }
}
