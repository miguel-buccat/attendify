<?php

namespace App\Http\Controllers;

use App\Enums\AttendanceMarkedBy;
use App\Enums\AttendanceStatus;
use App\Enums\SessionStatus;
use App\Models\AttendanceRecord;
use App\Models\ClassSession;
use App\Models\User;
use App\Notifications\AttendanceRecordedNotification;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicAttendanceController extends Controller
{
    public function show(string $sessionId, string $token): View
    {
        $session = ClassSession::with('schoolClass')->find($sessionId);

        if (! $session || $session->qr_token !== $token) {
            return view('attendance.public', [
                'error' => 'Invalid attendance link.',
                'session' => null,
            ]);
        }

        if ($session->status !== SessionStatus::Active) {
            return view('attendance.public', [
                'error' => 'This session is not currently active.',
                'session' => $session,
            ]);
        }

        if (now()->greaterThan($session->end_time)) {
            return view('attendance.public', [
                'error' => 'This session has ended.',
                'session' => $session,
            ]);
        }

        return view('attendance.public', [
            'error' => null,
            'session' => $session,
            'token' => $token,
        ]);
    }

    public function store(Request $request, string $sessionId, string $token)
    {
        $session = ClassSession::with('schoolClass')->find($sessionId);

        if (! $session || $session->qr_token !== $token) {
            return back()->withErrors(['email' => 'Invalid attendance link.']);
        }

        if ($session->status !== SessionStatus::Active) {
            return back()->withErrors(['email' => 'This session is not currently active.']);
        }

        if (now()->greaterThan($session->end_time)) {
            return back()->withErrors(['email' => 'This session has ended.']);
        }

        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $student = User::where('email', $request->input('email'))->first();

        if (! $student) {
            return back()->withInput()->withErrors(['email' => 'No account found with this email.']);
        }

        $isEnrolled = $session->schoolClass->students()
            ->where('student_id', $student->id)
            ->exists();

        if (! $isEnrolled) {
            return back()->withInput()->withErrors(['email' => 'You are not enrolled in this class.']);
        }

        $existingRecord = AttendanceRecord::where('class_session_id', $session->id)
            ->where('student_id', $student->id)
            ->exists();

        if ($existingRecord) {
            return redirect()->route('attend.show', [$sessionId, $token])
                ->with('attendance_success', 'Your attendance has already been recorded.');
        }

        $scannedAt = now();
        $graceDeadline = $session->start_time->copy()->addMinutes($session->grace_period_minutes);

        if ($scannedAt->greaterThan($graceDeadline)) {
            return back()->withInput()->withErrors(['email' => 'The grace period has passed. You cannot mark attendance.']);
        }

        $status = $scannedAt->lessThanOrEqualTo($session->start_time)
            ? AttendanceStatus::Present
            : AttendanceStatus::Late;

        $record = AttendanceRecord::create([
            'class_session_id' => $session->id,
            'student_id' => $student->id,
            'status' => $status,
            'scanned_at' => $scannedAt,
            'marked_by' => AttendanceMarkedBy::System,
        ]);

        $session->schoolClass->teacher->notify(new AttendanceRecordedNotification($record));

        return redirect()->route('attend.show', [$sessionId, $token])
            ->with('attendance_success', "Your attendance has been recorded as {$status->value}.");
    }
}
