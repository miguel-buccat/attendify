<?php

namespace App\Http\Controllers\Teacher;

use App\Enums\SessionStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\StartSessionRequest;
use App\Jobs\MarkAbsenteesAfterSession;
use App\Models\ActivityLog;
use App\Models\ClassSession;
use App\Models\SchoolClass;
use App\Notifications\ClassSessionStartedNotification;
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
        $recurrencePattern = $validated['recurrence_pattern'] ?? null;
        $recurrenceEndDate = isset($validated['recurrence_end_date']) ? Carbon::parse($validated['recurrence_end_date']) : null;
        $recurrenceGroupId = $recurrencePattern ? (string) Str::uuid() : null;

        $sessions = [];
        $startTime = Carbon::parse($validated['start_time']);
        $endTime = Carbon::parse($validated['end_time']);
        $gracePeriod = (int) ($validated['grace_period_minutes'] ?? 15);

        // Create first session (and recurring if applicable)
        $dates = [['start' => $startTime, 'end' => $endTime]];

        if ($recurrencePattern && $recurrenceEndDate) {
            $intervalDays = $recurrencePattern === 'biweekly' ? 14 : 7;
            $currentStart = $startTime->copy()->addDays($intervalDays);
            $currentEnd = $endTime->copy()->addDays($intervalDays);

            while ($currentStart->toDateString() <= $recurrenceEndDate->toDateString()) {
                $dates[] = ['start' => $currentStart->copy(), 'end' => $currentEnd->copy()];
                $currentStart->addDays($intervalDays);
                $currentEnd->addDays($intervalDays);
            }
        }

        foreach ($dates as $date) {
            $sessions[] = ClassSession::create([
                'class_id' => $class->id,
                'modality' => $validated['modality'],
                'location' => $validated['location'] ?? null,
                'start_time' => $date['start'],
                'end_time' => $date['end'],
                'grace_period_minutes' => $gracePeriod,
                'qr_token' => Str::random(64),
                'qr_expires_at' => $date['end']->copy()->addMinutes($gracePeriod),
                'status' => SessionStatus::Scheduled,
                'recurrence_pattern' => $recurrencePattern,
                'recurrence_end_date' => $recurrenceEndDate,
                'recurrence_group_id' => $recurrenceGroupId,
            ]);
        }

        $sessionCount = count($sessions);
        ActivityLog::log('created_session', "Scheduled {$sessionCount} session(s) for {$class->name}", $sessions[0]);

        $message = $sessionCount > 1
            ? "{$sessionCount} recurring sessions scheduled successfully."
            : 'Session scheduled successfully.';

        return redirect()->route('teacher.sessions.show', $sessions[0])
            ->with('success', $message);
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
