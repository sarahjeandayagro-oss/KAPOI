<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OverdueBookReminder extends Notification
{
    use Queueable;

    public function __construct(
        protected string $bookTitle,
        protected string $dueDate,
        protected int $daysOverdue,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Overdue Book Reminder — Panabo City Library')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line("Your borrowed book \"{$this->bookTitle}\" is overdue.")
            ->line("Due Date: {$this->dueDate} ({$this->daysOverdue} day(s) overdue)")
            ->line('Please return the book as soon as possible to avoid additional fines (PHP 10/day).')
            ->line('If you have already returned it, please disregard this message.')
            ->action('Login to Your Account', route('login'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Overdue Book Reminder',
            'message' => "\"{$this->bookTitle}\" is {$this->daysOverdue} day(s) overdue. Please return it.",
            'action_url' => route('login'),
        ];
    }
}
