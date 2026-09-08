<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FineIssued extends Notification
{
    use Queueable;

    public function __construct(
        protected string $description,
        protected float $amount,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Fine Issued — Panabo City Library')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line("A fine has been issued to your account.")
            ->line("Reason: {$this->description}")
            ->line('Amount: PHP ' . number_format($this->amount, 2))
            ->line('Please settle this fine at your earliest convenience.')
            ->action('View Your Account', route('login'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Fine Issued',
            'message' => "{$this->description} — PHP " . number_format($this->amount, 2),
            'action_url' => route('login'),
        ];
    }
}
