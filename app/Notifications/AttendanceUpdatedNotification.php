<?php

namespace App\Notifications;

use App\Models\AttendanceRecord;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AttendanceUpdatedNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly AttendanceRecord $record,
    ) {}

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /** @return array<string, mixed> */
    public function toArray(object $notifiable): array
    {
        $this->record->load('classSession.schoolClass');

        return [
            'title' => 'Attendance Updated',
            'body' => "Your attendance for {$this->record->classSession->schoolClass->name} has been updated to {$this->record->status->value}.",
            'icon' => 'edit',
            'url' => route('student.attendance.index'),

        ];
    }
}
