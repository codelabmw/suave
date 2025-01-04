<?php

namespace App\Contracts\Account;

interface CanSendTemporaryPassword
{
    /**
     * Send a temporary password to the user.
     */
    public function sendTemporaryPasswordNotification(): void;

    /**
     * Determine if the user must reset their password.
     */
    public function mustResetPassword(): bool;
}
