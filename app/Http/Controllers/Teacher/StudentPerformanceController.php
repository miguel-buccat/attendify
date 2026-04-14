<?php

namespace App\Http\Controllers\Teacher;

use App\Enums\AttendanceStatus;
use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class StudentPerformanceController extends Controller
{
    public function show(SchoolClass $class, User $student): View
    {
        Gate::authorize('view', $class);

        // Verify student is enrolled
        abort_unless(
            $class->students()->where('users.id', $student->id)->exists(),
            404,
            'Student is not enrolled in this class.'
        );

        $sessionIds = $class->sessions()->pluck('id');

        $records = $student->attendanceRecords()
            ->whereIn('class_session_id', $sessionIds)
            ->with('classSession')
            ->orderBy('created_at', 'desc')
            ->get();

        $totalRecords = $records->count();
        $presentCount = $records->where('status', AttendanceStatus::Present)->count();
        $lateCount = $records->where('status', AttendanceStatus::Late)->count();
        $absentCount = $records->where('status', AttendanceStatus::Absent)->count();
        $excusedCount = $records->where('status', AttendanceStatus::Excused)->count();
        $attendanceRate = $totalRecords > 0
            ? round(($presentCount + $lateCount) / $totalRecords * 100, 1)
            : 0;

        return view('teacher.students.show', compact(
            'class',
            'student',
            'records',
            'totalRecords',
            'presentCount',
            'lateCount',
            'absentCount',
            'excusedCount',
            'attendanceRate',
        ));
    }
}
