<?php

namespace App\Console\Commands;

use App\Enums\AttendanceStatus;
use App\Enums\ClassStatus;
use App\Enums\UserRole;
use App\Models\User;
use App\Notifications\WeeklyAttendanceSummaryNotification;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

#[Signature('app:send-weekly-reports')]
#[Description('Send weekly attendance summary notifications to all active teachers and students.')]
class SendWeeklyReports extends Command
{
    public function handle(): int
    {
        $weekStart = now()->startOfWeek();
        $weekEnd = now()->endOfWeek();

        $this->info('Sending weekly reports for '.$weekStart->format('M j').' – '.$weekEnd->format('M j, Y'));

        $studentCount = 0;
        $teacherCount = 0;

        // Students
        User::where('role', UserRole::Student)
            ->chunk(100, function ($students) use ($weekStart, $weekEnd, &$studentCount) {
                foreach ($students as $student) {
                    $records = $student->attendanceRecords()
                        ->whereHas('classSession', fn ($q) => $q->whereBetween('start_time', [$weekStart, $weekEnd]))
                        ->with('classSession.schoolClass')
                        ->get();

                    $present = $records->where('status', AttendanceStatus::Present)->count();
                    $late    = $records->where('status', AttendanceStatus::Late)->count();
                    $absent  = $records->where('status', AttendanceStatus::Absent)->count();
                    $excused = $records->where('status', AttendanceStatus::Excused)->count();
                    $total   = $records->count();
                    $rate    = $total > 0 ? round(($present + $late + $excused) / $total * 100, 1) : 0;

                    $classes = $records
                        ->groupBy(fn ($r) => $r->classSession->schoolClass->id)
                        ->map(function ($group) {
                            $class = $group->first()->classSession->schoolClass;
                            $sessionCount = $group->count();
                            $statusCounts = $group->countBy(fn ($r) => $r->status->value);
                            $dominant = $statusCounts->sortDesc()->keys()->first() ?? 'No sessions';

                            return [
                                'name'     => $class->name,
                                'sessions' => $sessionCount,
                                'status'   => $dominant,
                            ];
                        })
                        ->values()
                        ->toArray();

                    $student->notify(new WeeklyAttendanceSummaryNotification('student', [
                        'present' => $present,
                        'late'    => $late,
                        'absent'  => $absent,
                        'excused' => $excused,
                        'total'   => $total,
                        'rate'    => $rate,
                        'classes' => $classes,
                    ]));

                    $studentCount++;
                }
            });

        // Teachers
        User::where('role', UserRole::Teacher)
            ->chunk(100, function ($teachers) use ($weekStart, $weekEnd, &$teacherCount) {
                foreach ($teachers as $teacher) {
                    $classes = $teacher->classes()
                        ->where('status', ClassStatus::Active)
                        ->with(['sessions' => fn ($q) => $q->whereBetween('start_time', [$weekStart, $weekEnd])
                            ->with('attendanceRecords')])
                        ->get();

                    $classSummaries = $classes->map(function ($class) {
                        $sessions = $class->sessions;
                        $sessionCount = $sessions->count();

                        if ($sessionCount === 0) {
                            return null;
                        }

                        $allRecords = $sessions->flatMap->attendanceRecords;
                        $total    = $allRecords->count();
                        $present  = $allRecords->where('status', AttendanceStatus::Present)->count();
                        $late     = $allRecords->where('status', AttendanceStatus::Late)->count();
                        $absences = $allRecords->where('status', AttendanceStatus::Absent)->count();
                        $rate     = $total > 0 ? round(($present + $late) / $total * 100, 1) : 0;

                        return [
                            'name'     => $class->name,
                            'sessions' => $sessionCount,
                            'rate'     => $rate,
                            'absences' => $absences,
                        ];
                    })->filter()->values()->toArray();

                    $teacher->notify(new WeeklyAttendanceSummaryNotification('teacher', [
                        'classes' => $classSummaries,
                    ]));

                    $teacherCount++;
                }
            });

        $this->info("Sent to {$studentCount} students and {$teacherCount} teachers.");

        return self::SUCCESS;
    }
}
