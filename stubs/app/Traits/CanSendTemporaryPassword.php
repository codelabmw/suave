<?php

namespace App\Traits;

use App\Notifications\TemporaryPasswordNotification;

trait CanSendTemporaryPassword
{
    /**
     * Send a temporary password to the user.
     */
    public function sendTemporaryPasswordNotification(): void
    {
        $this->notify(new TemporaryPasswordNotification($this));
    }

    /**
     * Determine if the user must reset their password.
     */
    public function mustResetPassword(): bool
    {
        return (bool) $this->must_reset_password;
    }
}
