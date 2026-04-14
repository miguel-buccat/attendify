<?php

namespace App\Notifications;

use App\Models\AttendanceRecord;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AttendanceRecordedNotification extends Notification
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
        $this->record->load(['student', 'classSession.schoolClass']);

        return [
            'title' => 'Attendance Recorded',
            'body' => "{$this->record->student->name} marked as {$this->record->status->value} in {$this->record->classSession->schoolClass->name}.",
            'icon' => 'user-check',
            'url' => route('teacher.attendance.index', $this->record->class_session_id),

        ];
    }
}
