<?php

namespace App\Http\Controllers\Student;

use App\Enums\AttendanceStatus;
use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StudentDashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        $myClasses = $user->enrolledClasses()->count();

        $records = $user->attendanceRecords();
        $totalRecords = $records->count();
        $presentCount = (clone $records)->where('status', AttendanceStatus::Present)->count();
        $lateCount = (clone $records)->where('status', AttendanceStatus::Late)->count();
        $absentCount = (clone $records)->where('status', AttendanceStatus::Absent)->count();
        $excusedCount = (clone $records)->where('status', AttendanceStatus::Excused)->count();
        $attendanceRate = $totalRecords > 0
            ? round((($presentCount + $lateCount + $excusedCount) / $totalRecords) * 100, 1)
            : 0;

        // Attendance over time (last 12 weeks) for line chart
        $weeklyData = DB::table('attendance_records')
            ->select(
                DB::raw("date_trunc('week', created_at) as week"),
                DB::raw('count(*) as total'),
                DB::raw("count(*) filter (where status in ('Present', 'Late', 'Excused')) as attended"),
            )
            ->where('student_id', $user->id)
            ->where('created_at', '>=', now()->subWeeks(12))
            ->groupBy(DB::raw("date_trunc('week', created_at)"))
            ->orderBy('week')
            ->get();

        $lineData = [
            'labels' => $weeklyData->pluck('week')->map(fn ($w) => \Carbon\Carbon::parse($w)->format('M d'))->toArray(),
            'values' => $weeklyData->map(fn ($row) => $row->total > 0 ? round(($row->attended / $row->total) * 100, 1) : 0)->toArray(),
            'label' => 'Attendance Rate %',
        ];

        // Status breakdown pie chart
        $pieData = [
            'labels' => ['Present', 'Late', 'Absent', 'Excused'],
            'values' => [$presentCount, $lateCount, $absentCount, $excusedCount],
        ];

        // Recent attendance records
        $recentRecords = $user->attendanceRecords()
            ->with('classSession.schoolClass')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.student', compact(
            'user',
            'myClasses',
            'presentCount',
            'lateCount',
            'absentCount',
            'excusedCount',
            'attendanceRate',
            'lineData',
            'pieData',
            'recentRecords',
        ));
    }
}
