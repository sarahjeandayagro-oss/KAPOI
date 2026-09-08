<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountVerified extends Notification
{
    use Queueable;

    public function __construct() {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Account Verified — Panabo City Library')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Your library account has been verified by our staff.')
            ->line('You may now log in and access all library services.')
            ->action('Login to Your Account', route('login'))
            ->line('Welcome to Panabo City Library!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Account Verified',
            'message' => 'Your library account has been verified. You may now log in.',
            'action_url' => route('login'),
        ];
    }
}
