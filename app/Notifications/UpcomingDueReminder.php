<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UpcomingDueReminder extends Notification
{
    use Queueable;

    public function __construct(
        protected string $bookTitle,
        protected string $dueDate,
        protected int $daysUntilDue,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Upcoming Due Reminder — Panabo City Library')
            ->greeting('Hello ' . ($notifiable->name ?? 'Library User') . '!')
            ->line("This is a friendly reminder that your borrowed book \"{$this->bookTitle}\" is due in {$this->daysUntilDue} day(s).")
            ->line("Due Date: {$this->dueDate}")
            ->line('Return or renew the book to avoid overdue fines.')
            ->action('View Your Account', route('login'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Upcoming Due Reminder',
            'message' => "{$this->bookTitle} is due in {$this->daysUntilDue} day(s).",
            'action_url' => route('login'),
        ];
    }
}
