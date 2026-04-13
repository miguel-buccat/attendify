<?php

namespace App\Notifications;

use App\Models\ExcuseRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ExcuseSubmittedNotification extends Notification
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
        return [
            'title' => 'New Excuse Request',
            'body' => "{$this->excuseRequest->student->name} submitted an excuse for {$this->excuseRequest->schoolClass->name}.",
            'icon' => 'file-text',
            'url' => route('teacher.excuses.index'),

        ];
    }
}
