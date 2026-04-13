<?php

namespace App\Notifications;

use App\Models\ExcuseRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ExcuseReviewedNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly ExcuseRequest $excuseRequest,
    ) {}

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /** @return array<string, mixed> */
    public function toArray(object $notifiable): array
    {
        $status = $this->excuseRequest->status->value;

        return [
            'title' => "Excuse {$status}",
            'body' => "Your excuse for {$this->excuseRequest->schoolClass->name} has been {$status} by the teacher.",
            'icon' => $status === 'Acknowledged' ? 'check' : 'x',
            'url' => route('student.excuses.index'),

        ];
    }
}
