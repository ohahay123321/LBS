<?php

namespace App\Notifications;

use App\Models\BookRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BookReturned extends Notification
{
    use Queueable;

    public function __construct(
        public BookRequest $request
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'book_returned',
            'request_id' => $this->request->id,
            'book_id' => $this->request->book_id,
            'book_title' => $this->request->book->title ?? 'Unknown',
            'message' => "\"{$this->request->book->title}\" has been returned successfully.",
        ];
    }
}
