<?php

namespace App\Http\Controllers\Teacher;

use App\Enums\SessionStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\BulkScheduleSessionRequest;
use App\Http\Requests\Teacher\StartSessionRequest;
use App\Jobs\MarkAbsenteesAfterSession;
use App\Models\ActivityLog;
use App\Models\ClassSession;
use App\Models\SchoolClass;
use App\Notifications\ClassSessionStartedNotification;
use App\Notifications\SessionCompletedNotification;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ClassSessionController extends Controller
{
    public function store(StartSessionRequest $request, SchoolClass $class): RedirectResponse
    {
        Gate::authorize('enroll', $class);

        $validated = $request->validated();
        $startTime = Carbon::parse($validated['start_time']);
        $endTime = Carbon::parse($validated['end_time']);
        $gracePeriod = (int) ($validated['grace_period_minutes'] ?? 15);

        $session = ClassSession::create([
            'class_id' => $class->id,
            'modality' => $validated['modality'],
            'location' => $validated['location'] ?? null,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'grace_period_minutes' => $gracePeriod,
            'qr_token' => Str::random(64),
            'qr_expires_at' => $endTime->copy()->addMinutes($gracePeriod),
            'status' => SessionStatus::Scheduled,
        ]);

        ActivityLog::log('created_session', "Scheduled session for {$class->name}", $session);

        return redirect()->route('teacher.sessions.show', $session)
            ->with('success', 'Session scheduled successfully.');
    }

    public function bulkStore(BulkScheduleSessionRequest $request, SchoolClass $class): RedirectResponse
    {
        Gate::authorize('enroll', $class);

        $validated = $request->validated();
        $days = $validated['days'];
        $startTime = $validated['start_time'];
        $endTime = $validated['end_time'];
        $gracePeriod = (int) ($validated['grace_period_minutes'] ?? 15);
        $intervalWeeks = (int) ($validated['interval_weeks'] ?? 1);
        $startDate = Carbon::parse($validated['start_date']);
        $endDate = Carbon::parse($validated['end_date']);
        $recurrenceGroupId = (string) Str::uuid();

        $dayNumbers = array_map(fn (string $day) => match ($day) {
            'Monday' => Carbon::MONDAY,
            'Tuesday' => Carbon::TUESDAY,
            'Wednesday' => Carbon::WEDNESDAY,
            'Thursday' => Carbon::THURSDAY,
            'Friday' => Carbon::FRIDAY,
        }, $days);

        $sessions = [];
        $cursor = $startDate->copy();

        while ($cursor->lte($endDate)) {
            if (in_array($cursor->dayOfWeekIso, $dayNumbers, true)) {
                $sessionStart = $cursor->copy()->setTimeFromTimeString($startTime);
                $sessionEnd = $cursor->copy()->setTimeFromTimeString($endTime);

                if ($sessionStart->gt(now())) {
                    $sessions[] = ClassSession::create([
                        'class_id' => $class->id,
                        'modality' => $validated['modality'],
                        'location' => $validated['location'] ?? null,
                        'start_time' => $sessionStart,
                        'end_time' => $sessionEnd,
                        'grace_period_minutes' => $gracePeriod,
                        'qr_token' => Str::random(64),
                        'qr_expires_at' => $sessionEnd->copy()->addMinutes($gracePeriod),
                        'status' => SessionStatus::Scheduled,
                        'recurrence_pattern' => 'weekly',
                        'recurrence_end_date' => $endDate,
                        'recurrence_group_id' => $recurrenceGroupId,
                    ]);
                }
            }

            // After processing all days in the current week (Sunday), skip forward by interval
            if ($cursor->dayOfWeekIso === 7) {
                $cursor->addWeeks($intervalWeeks - 1);
            }

            $cursor->addDay();
        }

        $sessionCount = count($sessions);

        if ($sessionCount === 0) {
            return redirect()->route('teacher.classes.show', $class)
                ->with('success', 'No sessions were created — the selected days may already be in the past.');
        }

        ActivityLog::log('created_session', "Pre-scheduled {$sessionCount} session(s) for {$class->name}", $sessions[0]);

        return redirect()->route('teacher.classes.show', $class)
            ->with('success', "{$sessionCount} session(s) pre-scheduled successfully.");
    }

    public function show(ClassSession $session): View
    {
        Gate::authorize('view', $session);

        $session->load(['schoolClass', 'attendanceRecords.student']);
        $enrolledCount = $session->schoolClass->students()->count();
        $scannedCount = $session->attendanceRecords->count();

        return view('teacher.sessions.show', compact('session', 'enrolledCount', 'scannedCount'));
    }

    public function start(ClassSession $session): RedirectResponse
    {
        Gate::authorize('start', $session);

        $session->update(['status' => SessionStatus::Active]);

        ActivityLog::log('started_session', "Started session for {$session->schoolClass->name}", $session);

        $session->load('schoolClass.students');
        $session->schoolClass->students->each(
            fn ($student) => $student->notify(new ClassSessionStartedNotification($session))
        );

        return redirect()->route('teacher.sessions.show', $session)
            ->with('success', 'Session started. QR code is now active.');
    }

    public function complete(ClassSession $session): RedirectResponse
    {
        Gate::authorize('complete', $session);

        $session->update(['status' => SessionStatus::Completed]);

        ActivityLog::log('completed_session', "Completed session for {$session->schoolClass->name}", $session);

        MarkAbsenteesAfterSession::dispatch($session);

        $session->load('schoolClass.students');
        $session->schoolClass->students->each(
            fn ($student) => $student->notify(new SessionCompletedNotification($session))
        );

        return redirect()->route('teacher.sessions.show', $session)
            ->with('success', 'Session completed.');
    }

    public function cancel(ClassSession $session): RedirectResponse
    {
        Gate::authorize('cancel', $session);

        $session->update([
            'status' => SessionStatus::Cancelled,
            'cancellation_reason' => request()->input('cancellation_reason'),
        ]);

        ActivityLog::log('cancelled_session', "Cancelled session for {$session->schoolClass->name}", $session);

        return redirect()->route('teacher.sessions.show', $session)
            ->with('success', 'Session cancelled.');
    }

    public function cancelUpcoming(ClassSession $session): RedirectResponse
    {
        Gate::authorize('cancel', $session);

        $reason = request()->input('cancellation_reason', 'Cancelled by teacher');

        $upcomingSessions = ClassSession::where('recurrence_group_id', $session->recurrence_group_id)
            ->where('start_time', '>=', now())
            ->whereIn('status', [SessionStatus::Scheduled])
            ->get();

        foreach ($upcomingSessions as $upcoming) {
            $upcoming->update([
                'status' => SessionStatus::Cancelled,
                'cancellation_reason' => $reason,
            ]);
        }

        ActivityLog::log('cancelled_recurring_sessions', "Cancelled {$upcomingSessions->count()} upcoming sessions for {$session->schoolClass->name}", $session);

        return redirect()->route('teacher.classes.show', $session->class_id)
            ->with('success', $upcomingSessions->count().' upcoming session(s) cancelled.');
    }

    public function attendanceData(ClassSession $session): JsonResponse
    {
        Gate::authorize('view', $session);

        $session->load('attendanceRecords.student');

        $records = $session->attendanceRecords->map(fn ($record) => [
            'student_name' => $record->student->name,
            'status' => $record->status->value,
            'scanned_at' => $record->scanned_at?->format('g:i A'),
        ]);

        return response()->json([
            'scanned_count' => $session->attendanceRecords->count(),
            'records' => $records,
            'session_status' => $session->status->value,
        ]);
    }
}
