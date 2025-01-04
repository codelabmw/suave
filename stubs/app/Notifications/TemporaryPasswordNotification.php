<?php

namespace App\Notifications;

use App\Models\User;

use Codelabmw\Testament\Enums\CodeType;
use Codelabmw\Testament\Testament;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Hash;

class TemporaryPasswordNotification extends Notification
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
        $newPassword = Testament::default()->generate(
            type: CodeType::PASSWORD,
            length: config('suave.temporary_password_length', 8),
        );

        $this->user->update([
            'password' => Hash::make($newPassword),
            'must_reset_password' => true,
        ]);

        return (new MailMessage)
            ->subject('Forgot Password')
            ->line('We received a request to reset your password. Please use the following temporary password to login:')
            ->line($newPassword)
            ->line('We strongly recommend that you change your password after logging in.')
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
