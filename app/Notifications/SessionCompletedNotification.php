<?php

namespace App\Notifications;

use App\Models\ClassSession;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SessionCompletedNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly ClassSession $session,
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
            'title' => 'Session Completed',
            'body' => "{$this->session->schoolClass->name} session has ended.",
            'icon' => 'check-circle',
            'url' => route('student.attendance.index'),

        ];
    }
}
