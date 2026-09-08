<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TransactionPaid extends Notification
{
    use Queueable;

    public function __construct(
        protected string $transactionType,
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
            ->subject('Payment Received — Panabo City Library')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line("Your {$this->transactionType} transaction has been marked as paid.")
            ->line("Details: {$this->description}")
            ->line('Amount: PHP ' . number_format($this->amount, 2))
            ->line('Thank you for settling your account.')
            ->action('View Your Account', route('login'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Payment Received',
            'message' => "Your {$this->transactionType} transaction '{$this->description}' has been paid. PHP " . number_format($this->amount, 2),
            'action_url' => route('login'),
        ];
    }
}
