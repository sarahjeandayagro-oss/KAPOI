<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class BarcodeRequestStatusUpdated extends Notification
{
    use Queueable;

    protected $status;
    protected $note;
    protected $newBarcodeId;
    protected $requestId;

    public function __construct(string $status, ?string $note = null, ?string $newBarcodeId = null, ?int $requestId = null)
    {
        $this->status = $status;
        $this->note = $note;
        $this->newBarcodeId = $newBarcodeId;
        $this->requestId = $requestId;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'barcode_request_status',
            'request_id' => $this->requestId,
            'status' => $this->status,
            'note' => $this->note,
            'new_barcode_id' => $this->newBarcodeId,
            'message' => $this->status === 'approved'
                ? 'Your barcode request was approved.'
                : 'Your barcode request was rejected.',
        ];
    }
}
