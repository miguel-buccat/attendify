<?php

namespace App\Http\Controllers\Teacher;

use App\Enums\AttendanceMarkedBy;
use App\Enums\AttendanceStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\UpdateAttendanceRequest;
use App\Models\ActivityLog;
use App\Models\AttendanceRecord;
use App\Models\ClassSession;
use App\Notifications\ParentAbsenceNotification;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AttendanceController extends Controller
{
    public function index(ClassSession $session): View
    {
        Gate::authorize('viewAny', [AttendanceRecord::class, $session]);

        $session->load(['schoolClass', 'attendanceRecords.student']);

        $enrolledStudents = $session->schoolClass->students()->orderBy('name')->get();
        $recordsByStudent = $session->attendanceRecords->keyBy('student_id');

        return view('teacher.sessions.attendance', compact('session', 'enrolledStudents', 'recordsByStudent'));
    }

    public function update(UpdateAttendanceRequest $request, AttendanceRecord $record): RedirectResponse
    {
        Gate::authorize('update', $record);

        $validated = $request->validated();

        $record->update([
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
            'marked_by' => AttendanceMarkedBy::Teacher,
        ]);

        ActivityLog::log('updated_attendance', "Marked {$record->student->name} as {$validated['status']}", $record);

        // Notify parent/guardian if student is marked absent
        if ($record->status === AttendanceStatus::Absent) {
            $student = $record->student;
            if ($student->guardian_email) {
                $record->load(['classSession.schoolClass']);
                (new AnonymousNotifiable)
                    ->route('mail', $student->guardian_email)
                    ->notify(new ParentAbsenceNotification($record));
            }
        }

        return redirect()->route('teacher.attendance.index', $record->class_session_id)
            ->with('success', 'Attendance record updated.');
    }

    public function export(ClassSession $session): StreamedResponse
    {
        Gate::authorize('viewAny', [AttendanceRecord::class, $session]);

        $session->load(['schoolClass', 'attendanceRecords.student']);

        $enrolledStudents = $session->schoolClass->students()->orderBy('name')->get();
        $recordsByStudent = $session->attendanceRecords->keyBy('student_id');

        $filename = str_replace(' ', '_', $session->schoolClass->name).'_session_'.$session->start_time->format('Y-m-d').'.csv';

        return response()->streamDownload(function () use ($enrolledStudents, $recordsByStudent) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Student Name', 'Email', 'Status', 'Scanned At', 'Marked By', 'Notes']);

            foreach ($enrolledStudents as $student) {
                $record = $recordsByStudent->get($student->id);
                fputcsv($handle, [
                    $student->name,
                    $student->email,
                    $record?->status->value ?? 'No Record',
                    $record?->scanned_at?->format('Y-m-d H:i:s') ?? '',
                    $record?->marked_by->value ?? '',
                    $record?->notes ?? '',
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function exportPdf(ClassSession $session): Response
    {
        Gate::authorize('viewAny', [AttendanceRecord::class, $session]);

        $session->load(['schoolClass.teacher', 'attendanceRecords.student']);

        $students = $session->schoolClass->students()->orderBy('name')->get();
        $recordsByStudent = $session->attendanceRecords->keyBy('student_id');

        $presentCount = 0;
        $lateCount = 0;
        $absentCount = 0;
        $excusedCount = 0;
        $noRecordCount = 0;

        foreach ($students as $student) {
            $record = $recordsByStudent->get($student->id);
            match ($record?->status) {
                AttendanceStatus::Present => $presentCount++,
                AttendanceStatus::Late => $lateCount++,
                AttendanceStatus::Absent => $absentCount++,
                AttendanceStatus::Excused => $excusedCount++,
                default => $noRecordCount++,
            };
        }

        $filename = str_replace(' ', '_', $session->schoolClass->name).'_session_'.$session->start_time->format('Y-m-d').'.pdf';

        $pdf = Pdf::loadView('reports.session-attendance', compact(
            'session',
            'students',
            'recordsByStudent',
            'presentCount',
            'lateCount',
            'absentCount',
            'excusedCount',
            'noRecordCount',
        ));

        return $pdf->download($filename);
    }
}
