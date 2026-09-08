<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewAnnouncement extends Notification
{
    use Queueable;

    public function __construct(
        protected string $title,
        protected string $body,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->title . ' — Panabo City Library')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line($this->body)
            ->action('Login to Library Portal', route('login'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'message' => $this->body,
            'action_url' => route('login'),
        ];
    }
}
