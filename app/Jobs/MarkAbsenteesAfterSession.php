<?php

namespace App\Jobs;

use App\Enums\AttendanceMarkedBy;
use App\Enums\AttendanceStatus;
use App\Enums\ExcuseRequestStatus;
use App\Models\AttendanceRecord;
use App\Models\ClassSession;
use App\Models\ExcuseRequest;
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

        // Check for acknowledged excuse requests for this session's class and date
        $sessionDate = $this->session->start_time->toDateString();
        $excusedStudentIds = ExcuseRequest::where('class_id', $this->session->class_id)
            ->where('excuse_date', $sessionDate)
            ->where('status', ExcuseRequestStatus::Acknowledged)
            ->whereIn('student_id', $absentStudentIds)
            ->pluck('student_id');

        $records = $absentStudentIds->map(fn ($studentId) => [
            'class_session_id' => $this->session->id,
            'student_id' => $studentId,
            'status' => $excusedStudentIds->contains($studentId)
                ? AttendanceStatus::Excused->value
                : AttendanceStatus::Absent->value,
            'scanned_at' => null,
            'marked_by' => AttendanceMarkedBy::System->value,
            'notes' => $excusedStudentIds->contains($studentId) ? 'Auto-excused via approved excuse request' : null,
            'created_at' => now(),
            'updated_at' => now(),
        ])->all();

        AttendanceRecord::insert($records);
    }
}
