<?php

namespace App\Notifications;

use App\Models\BookRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewBookRequest extends Notification
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
            'type' => 'new_request',
            'request_id' => $this->request->id,
            'book_id' => $this->request->book_id,
            'book_title' => $this->request->book->title ?? 'Unknown',
            'student_name' => $this->request->student_name,
            'student_id_num' => $this->request->student_id_num,
            'message' => "{$this->request->student_name} requested \"{$this->request->book->title}\"",
        ];
    }
}
