<?php

namespace App\Jobs;

use App\Enums\AttendanceMarkedBy;
use App\Enums\AttendanceStatus;
use App\Enums\ExcuseRequestStatus;
use App\Models\AttendanceRecord;
use App\Models\ClassSession;
use App\Models\ExcuseRequest;
use App\Models\User;
use App\Notifications\ParentAbsenceNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Notifications\AnonymousNotifiable;

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

        // Notify parents of truly-absent students (not excused)
        $this->session->load('schoolClass');
        $studentsToNotify = User::whereIn('id', $absentStudentIds->diff($excusedStudentIds))
            ->whereNotNull('guardian_email')
            ->get();

        foreach ($studentsToNotify as $student) {
            $record = AttendanceRecord::where('class_session_id', $this->session->id)
                ->where('student_id', $student->id)
                ->first();

            if ($record) {
                $record->setRelation('student', $student);
                $record->setRelation('classSession', $this->session);

                (new AnonymousNotifiable)
                    ->route('mail', $student->guardian_email)
                    ->notify(new ParentAbsenceNotification($record));
            }
        }
    }
}
