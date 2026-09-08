<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProposalStatusUpdated extends Notification
{
    use Queueable;

    public function __construct(
        protected string $proposalTitle,
        protected string $status,
        protected string $feedback = '',
        protected ?float $approvedGrant = null,
        /**
         * Optional array of recent comments made by staff/admin on the proposal.
         * Each item should be an associative array with keys: commenter_role, page, location, comment_text, created_at
         */
        protected array $comments = [],
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject("Proposal {$this->status} — Panabo City Library")
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line("Your research proposal \"{$this->proposalTitle}\" has been {$this->status}.");

        if ($this->approvedGrant) {
            $mail->line('Approved Grant: PHP ' . number_format($this->approvedGrant, 2));
        }
        if ($this->feedback) {
            $mail->line("Feedback: {$this->feedback}");
        }

        // Include recent comments if provided
        if (!empty($this->comments)) {
            $mail->line('Comments / Reviewer notes:');
            foreach ($this->comments as $c) {
                $who = $c['commenter_role'] ?? 'Staff';
                $when = isset($c['created_at']) ? (' on ' . date('M d, Y', strtotime($c['created_at']))) : '';
                $meta = [];
                if (!empty($c['page'])) $meta[] = 'page '.$c['page'];
                if (!empty($c['location'])) $meta[] = $c['location'];
                $metaText = $meta ? ' (' . implode(', ', $meta) . ')' : '';
                $mail->line("- [{$who}]{$when}{$metaText}: " . trim(substr($c['comment_text'], 0, 400))); // truncate to keep mail concise
            }
        }

        return $mail
            ->action('View Dashboard', route('researcher.dashboard'))
            ->line('Thank you for your submission.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => "Proposal {$this->status}",
            'message' => "Your proposal \"{$this->proposalTitle}\" has been {$this->status}.",
            'action_url' => route('researcher.dashboard'),
        ];
    }
}
