<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AttendanceStatus;
use App\Enums\ClassStatus;
use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use App\Models\ClassSession;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        $usersByRole = User::query()
            ->select('role', DB::raw('count(*) as count'))
            ->groupBy('role')
            ->pluck('count', 'role');

        $totalUsers = $usersByRole->sum();
        $activeClasses = SchoolClass::where('status', ClassStatus::Active)->count();
        $sessionsThisMonth = ClassSession::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $totalRecords = AttendanceRecord::count();
        $presentAndLate = AttendanceRecord::whereIn('status', [AttendanceStatus::Present, AttendanceStatus::Late])->count();
        $avgAttendanceRate = $totalRecords > 0 ? round(($presentAndLate / $totalRecords) * 100, 1) : 0;

        // Attendance distribution for pie chart
        $attendanceDistribution = AttendanceRecord::query()
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');

        $pieData = [
            'labels' => $attendanceDistribution->keys()->map(fn ($s) => $s->value ?? $s)->values()->toArray(),
            'values' => $attendanceDistribution->values()->toArray(),
        ];

        // Attendance trend (last 30 days)
        $trendData = DB::table('attendance_records')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as total'), DB::raw("count(*) filter (where status in ('Present', 'Late')) as attended"))
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get();

        $lineData = [
            'labels' => $trendData->pluck('date')->map(fn ($d) => \Carbon\Carbon::parse($d)->format('M d'))->toArray(),
            'values' => $trendData->map(fn ($row) => $row->total > 0 ? round(($row->attended / $row->total) * 100, 1) : 0)->toArray(),
            'label' => 'Attendance Rate %',
        ];

        return view('dashboard.admin', compact(
            'user',
            'totalUsers',
            'activeClasses',
            'sessionsThisMonth',
            'avgAttendanceRate',
            'pieData',
            'lineData',
        ));
    }
}
