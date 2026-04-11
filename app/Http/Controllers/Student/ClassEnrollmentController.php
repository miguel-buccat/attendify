<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ClassEnrollmentController extends Controller
{
    public function index(): View
    {
        $classes = auth()->user()->enrolledClasses()
            ->withCount('students')
            ->orderByDesc('pivot_enrolled_at')
            ->get();

        return view('student.classes.index', compact('classes'));
    }

    public function show(SchoolClass $class): View
    {
        $user = auth()->user();

        // Verify the student is enrolled
        abort_unless(
            $class->students()->where('student_id', $user->id)->exists(),
            403,
        );

        $class->load('teacher');

        // Get sessions for this class (most recent first)
        $sessions = $class->sessions()
            ->orderByDesc('start_time')
            ->get();

        // Get this student's attendance records for this class, keyed by session ID
        $records = $user->attendanceRecords()
            ->whereIn('class_session_id', $sessions->pluck('id'))
            ->get()
            ->keyBy('class_session_id');

        // Attendance stats for this class
        $totalRecords = $records->count();
        $presentCount = $records->where('status.value', 'Present')->count();
        $lateCount = $records->where('status.value', 'Late')->count();
        $absentCount = $records->where('status.value', 'Absent')->count();
        $excusedCount = $records->where('status.value', 'Excused')->count();
        $attendanceRate = $totalRecords > 0
            ? round((($presentCount + $lateCount + $excusedCount) / $totalRecords) * 100, 1)
            : 0;

        // Pie chart data
        $pieData = [
            'labels' => ['Present', 'Late', 'Absent', 'Excused'],
            'values' => [$presentCount, $lateCount, $absentCount, $excusedCount],
        ];

        // Attendance over time (per session) for line chart
        $completedSessions = $sessions->where('status.value', 'Completed')->sortBy('start_time')->values();
        $lineData = [
            'labels' => $completedSessions->map(fn ($s) => $s->start_time->format('M d'))->toArray(),
            'values' => $completedSessions->map(function ($s) use ($records) {
                $record = $records->get($s->id);
                if (! $record) {
                    return 0;
                }

                return in_array($record->status->value, ['Present', 'Late', 'Excused']) ? 100 : 0;
            })->toArray(),
            'label' => 'Attendance %',
        ];

        return view('student.classes.show', compact(
            'class',
            'sessions',
            'records',
            'presentCount',
            'lateCount',
            'absentCount',
            'excusedCount',
            'attendanceRate',
            'pieData',
            'lineData',
        ));
    }
}
