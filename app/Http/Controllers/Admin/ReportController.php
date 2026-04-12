<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AttendanceStatus;
use App\Enums\ClassStatus;
use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use App\Models\SchoolClass;
use App\Support\SiteSettings;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request, SiteSettings $settings): View
    {
        $classesRanked = $this->getClassesRankedByAttendance();

        return view('admin.reports.index', compact('classesRanked'));
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $from = $request->input('from') ? Carbon::parse($request->input('from')) : now()->subMonth();
        $to = $request->input('to') ? Carbon::parse($request->input('to')) : now();

        $records = AttendanceRecord::with(['student', 'classSession.schoolClass'])
            ->whereBetween('created_at', [$from->startOfDay(), $to->endOfDay()])
            ->orderBy('created_at', 'desc')
            ->get();

        $filename = 'attendify_attendance_report_'.$from->format('Y-m-d').'_to_'.$to->format('Y-m-d').'.csv';

        return response()->streamDownload(function () use ($records) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Class', 'Session Date', 'Student Name', 'Email', 'Status', 'Scanned At', 'Marked By', 'Notes']);

            foreach ($records as $record) {
                fputcsv($handle, [
                    $record->classSession?->schoolClass?->name ?? 'N/A',
                    $record->classSession?->start_time?->format('Y-m-d H:i') ?? '',
                    $record->student?->name ?? 'N/A',
                    $record->student?->email ?? '',
                    $record->status->value,
                    $record->scanned_at?->format('Y-m-d H:i:s') ?? '',
                    $record->marked_by->value,
                    $record->notes ?? '',
                ]);
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    public function exportPdf(Request $request, SiteSettings $settings)
    {
        $from = $request->input('from') ? Carbon::parse($request->input('from')) : now()->subMonth();
        $to = $request->input('to') ? Carbon::parse($request->input('to')) : now();

        $records = AttendanceRecord::with(['student', 'classSession.schoolClass'])
            ->whereBetween('created_at', [$from->startOfDay(), $to->endOfDay()])
            ->orderBy('created_at', 'desc')
            ->get();

        $totalRecords = $records->count();
        $presentCount = $records->where('status', AttendanceStatus::Present)->count();
        $lateCount = $records->where('status', AttendanceStatus::Late)->count();
        $absentCount = $records->where('status', AttendanceStatus::Absent)->count();
        $excusedCount = $records->where('status', AttendanceStatus::Excused)->count();
        $attendanceRate = $totalRecords > 0 ? round(($presentCount + $lateCount) / $totalRecords * 100, 1) : 0;

        $classesRanked = $this->getClassesRankedByAttendance();
        $institutionName = $settings->get('institution_name', 'Attendify');

        $pdf = Pdf::loadView('reports.admin-attendance', compact(
            'records',
            'from',
            'to',
            'totalRecords',
            'presentCount',
            'lateCount',
            'absentCount',
            'excusedCount',
            'attendanceRate',
            'classesRanked',
            'institutionName',
        ));

        $pdf->setPaper('a4', 'portrait');

        return $pdf->download('attendify_attendance_report_'.$from->format('Y-m-d').'_to_'.$to->format('Y-m-d').'.pdf');
    }

    public function leaderboard(): View
    {
        $classesRanked = $this->getClassesRankedByAttendance();

        return view('admin.leaderboard.index', compact('classesRanked'));
    }

    private function getClassesRankedByAttendance(): Collection
    {
        return SchoolClass::where('status', ClassStatus::Active)
            ->with('teacher')
            ->withCount(['sessions as total_sessions'])
            ->get()
            ->map(function (SchoolClass $class) {
                $sessionIds = $class->sessions()->pluck('id');
                $totalRecords = AttendanceRecord::whereIn('class_session_id', $sessionIds)->count();
                $attended = AttendanceRecord::whereIn('class_session_id', $sessionIds)
                    ->whereIn('status', [AttendanceStatus::Present, AttendanceStatus::Late])
                    ->count();

                $class->attendance_rate = $totalRecords > 0 ? round($attended / $totalRecords * 100, 1) : 0;
                $class->total_records = $totalRecords;
                $class->student_count = $class->students()->count();

                return $class;
            })
            ->sortByDesc('attendance_rate')
            ->values();
    }
}
