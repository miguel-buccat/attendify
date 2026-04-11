<?php

namespace App\Http\Controllers\Teacher;

use App\Enums\AttendanceStatus;
use App\Enums\ClassStatus;
use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TeacherDashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        $classIds = $user->classes()->where('status', ClassStatus::Active)->pluck('id');
        $myClasses = $classIds->count();

        $totalStudents = DB::table('class_student')
            ->whereIn('class_id', $classIds)
            ->distinct('student_id')
            ->count('student_id');

        $sessionIds = DB::table('class_sessions')
            ->whereIn('class_id', $classIds)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->pluck('id');
        $sessionsThisMonth = $sessionIds->count();

        $allSessionIds = DB::table('class_sessions')->whereIn('class_id', $classIds)->pluck('id');
        $totalRecords = AttendanceRecord::whereIn('class_session_id', $allSessionIds)->count();
        $presentAndLate = AttendanceRecord::whereIn('class_session_id', $allSessionIds)
            ->whereIn('status', [AttendanceStatus::Present, AttendanceStatus::Late])
            ->count();
        $avgAttendanceRate = $totalRecords > 0 ? round(($presentAndLate / $totalRecords) * 100, 1) : 0;

        // Per-class attendance rate for bar chart
        $classes = $user->classes()->where('status', ClassStatus::Active)->get();
        $barLabels = [];
        $barValues = [];
        foreach ($classes as $class) {
            $classSessionIds = $class->sessions()->pluck('id');
            $classTotal = AttendanceRecord::whereIn('class_session_id', $classSessionIds)->count();
            $classAttended = AttendanceRecord::whereIn('class_session_id', $classSessionIds)
                ->whereIn('status', [AttendanceStatus::Present, AttendanceStatus::Late])
                ->count();
            $barLabels[] = $class->name;
            $barValues[] = $classTotal > 0 ? round(($classAttended / $classTotal) * 100, 1) : 0;
        }
        $barData = ['labels' => $barLabels, 'values' => $barValues, 'label' => 'Attendance Rate %'];

        // Attendance distribution pie chart
        $distribution = AttendanceRecord::whereIn('class_session_id', $allSessionIds)
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');

        $pieData = [
            'labels' => $distribution->keys()->map(fn ($s) => $s->value ?? $s)->values()->toArray(),
            'values' => $distribution->values()->toArray(),
        ];

        return view('dashboard.teacher', compact(
            'user',
            'myClasses',
            'totalStudents',
            'sessionsThisMonth',
            'avgAttendanceRate',
            'barData',
            'pieData',
        ));
    }
}
