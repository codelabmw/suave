<?php

namespace App\Notifications;

use App\Models\User;

use Codelabmw\Testament\Testament;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmailVerificationNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(private User $user)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $verificationCode = $this->user->verificationCodes()->create([
            'code' => Testament::default()->generate(
                type: config('suave.verification_code_type'),
                length: config('suave.verification_code_length'),
            ),
            'expires_at' => now()->addMinutes(config('suave.verification_code_expires_in')),
        ]);

        return (new MailMessage)
            ->subject('Email Verification')
            ->greeting('Hi ' . $this->user->name . ',')
            ->line('Use the following code to verify your email address. Please note that this code will expire in 15 minutes.')
            ->line('Verification Code: ' . $verificationCode->code)
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
