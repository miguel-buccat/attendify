<?php

namespace App\Http\Controllers\Teacher;

use App\Enums\SessionStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\StartSessionRequest;
use App\Jobs\MarkAbsenteesAfterSession;
use App\Models\ClassSession;
use App\Models\SchoolClass;
use App\Notifications\ClassSessionStartedNotification;
use Carbon\Carbon;
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

        $session = ClassSession::create([
            'class_id' => $class->id,
            'modality' => $validated['modality'],
            'location' => $validated['location'] ?? null,
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'grace_period_minutes' => (int) ($validated['grace_period_minutes'] ?? 15),
            'qr_token' => Str::random(64),
            'qr_expires_at' => Carbon::parse($validated['end_time'])->addMinutes((int) ($validated['grace_period_minutes'] ?? 15)),
            'status' => SessionStatus::Scheduled,
        ]);

        return redirect()->route('teacher.sessions.show', $session)
            ->with('success', 'Session scheduled successfully.');
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

        MarkAbsenteesAfterSession::dispatch($session);

        return redirect()->route('teacher.sessions.show', $session)
            ->with('success', 'Session completed.');
    }

    public function cancel(ClassSession $session): RedirectResponse
    {
        Gate::authorize('cancel', $session);

        $session->update(['status' => SessionStatus::Cancelled]);

        return redirect()->route('teacher.sessions.show', $session)
            ->with('success', 'Session cancelled.');
    }

    public function attendanceData(ClassSession $session): \Illuminate\Http\JsonResponse
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
