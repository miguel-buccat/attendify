<?php

namespace App\Http\Controllers\Teacher;

use App\Enums\AttendanceStatus;
use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Support\SiteSettings;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Gate;

class ClassAnalyticsController extends Controller
{
    public function exportPdf(SchoolClass $class, SiteSettings $settings)
    {
        Gate::authorize('view', $class);

        $class->load(['teacher', 'students', 'sessions.attendanceRecords.student']);

        $sessions = $class->sessions()
            ->orderBy('start_time', 'asc')
            ->with('attendanceRecords.student')
            ->get();

        $students = $class->students()->orderBy('name')->get();

        // Build attendance matrix: student -> session -> status
        $matrix = [];
        foreach ($students as $student) {
            $matrix[$student->id] = [
                'name' => $student->name,
                'email' => $student->email,
                'sessions' => [],
                'present' => 0,
                'late' => 0,
                'absent' => 0,
                'excused' => 0,
                'rate' => 0,
            ];
        }

        foreach ($sessions as $session) {
            $recordsByStudent = $session->attendanceRecords->keyBy('student_id');
            foreach ($students as $student) {
                $record = $recordsByStudent->get($student->id);
                $status = $record?->status?->value ?? 'No Record';
                $matrix[$student->id]['sessions'][$session->id] = $status;

                if ($record) {
                    match ($record->status) {
                        AttendanceStatus::Present => $matrix[$student->id]['present']++,
                        AttendanceStatus::Late => $matrix[$student->id]['late']++,
                        AttendanceStatus::Absent => $matrix[$student->id]['absent']++,
                        AttendanceStatus::Excused => $matrix[$student->id]['excused']++,
                    };
                }
            }
        }

        // Calculate rates
        $totalSessions = $sessions->count();
        foreach ($matrix as &$row) {
            $row['rate'] = $totalSessions > 0
                ? round(($row['present'] + $row['late']) / $totalSessions * 100, 1)
                : 0;
        }
        unset($row);

        $institutionName = $settings->get('institution_name', 'Attendify');

        $pdf = Pdf::loadView('reports.class-analytics', compact(
            'class',
            'sessions',
            'students',
            'matrix',
            'totalSessions',
            'institutionName',
        ));

        $pdf->setPaper('a4', 'landscape');

        $filename = str_replace(' ', '_', $class->name).'_analytics_'.now()->format('Y-m-d').'.pdf';

        return $pdf->download($filename);
    }
}
