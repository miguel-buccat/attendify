<?php

namespace App\Jobs;

use App\Enums\AttendanceMarkedBy;
use App\Enums\AttendanceStatus;
use App\Models\AttendanceRecord;
use App\Models\ClassSession;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class MarkAbsenteesAfterSession implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public ClassSession $session,
    ) {}

    public function handle(): void
    {
        $enrolledStudentIds = $this->session->schoolClass
            ->students()
            ->pluck('users.id');

        $recordedStudentIds = $this->session->attendanceRecords()
            ->pluck('student_id');

        $absentStudentIds = $enrolledStudentIds->diff($recordedStudentIds);

        $records = $absentStudentIds->map(fn ($studentId) => [
            'class_session_id' => $this->session->id,
            'student_id' => $studentId,
            'status' => AttendanceStatus::Absent->value,
            'scanned_at' => null,
            'marked_by' => AttendanceMarkedBy::System->value,
            'created_at' => now(),
            'updated_at' => now(),
        ])->all();

        AttendanceRecord::insert($records);
    }
}
