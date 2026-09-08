<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BorrowRequestStatusUpdated extends Notification
{
    use Queueable;

    protected $status;
    protected $note;
    protected $transactionId;
    protected $bookTitle;

    public function __construct(string $status, ?string $note = null, ?int $transactionId = null, ?string $bookTitle = null)
    {
        $this->status = $status;
        $this->note = $note;
        $this->transactionId = $transactionId;
        $this->bookTitle = $bookTitle;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        $book = $this->bookTitle ?: 'a book';
        $message = $this->status === 'approved'
            ? "Your borrow request for \"{$book}\" was approved. You may claim the book at the library."
            : "Your borrow request for \"{$book}\" was rejected."
                . ($this->note ? " Reason: {$this->note}" : '');

        return [
            'type' => 'borrow_request_status',
            'transaction_id' => $this->transactionId,
            'status' => $this->status,
            'note' => $this->note,
            'book_title' => $this->bookTitle,
            'message' => $message,
        ];
    }
}
