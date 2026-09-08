<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmailVerificationCode extends Notification
{
    use Queueable;

    public function __construct(protected string $code)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Email Verification Code — Panabo City Library')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Your verification code is ' . $this->code . '.')
            ->line('Enter this code on the verify email page to continue.')
            ->line('The code expires in 15 minutes.')
            ->action('Verify Email', route('email.verify.form'))
            ->line('If you did not create an account, please ignore this message.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Email verification code sent',
            'message' => 'A verification code was sent to your email address.',
        ];
    }
}
