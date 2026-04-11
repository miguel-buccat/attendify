<?php

namespace App\Http\Controllers\Student;

use App\Enums\AttendanceMarkedBy;
use App\Enums\AttendanceStatus;
use App\Enums\SessionStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Student\ScanAttendanceRequest;
use App\Models\AttendanceRecord;
use App\Models\ClassSession;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class AttendanceScanController extends Controller
{
    public function index(): View
    {
        return view('student.scan');
    }

    public function store(ScanAttendanceRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $session = ClassSession::find($validated['session_id']);

        if (! $session || $session->qr_token !== $validated['token']) {
            return response()->json(['message' => 'Invalid QR code.'], 422);
        }

        if ($session->status !== SessionStatus::Active) {
            return response()->json(['message' => 'This session is not currently active.'], 422);
        }

        if (now()->greaterThan($session->end_time)) {
            return response()->json(['message' => 'This session has ended.'], 422);
        }

        $student = $request->user();
        $isEnrolled = $session->schoolClass->students()
            ->where('student_id', $student->id)
            ->exists();

        if (! $isEnrolled) {
            return response()->json(['message' => 'You are not enrolled in this class.'], 403);
        }

        $existingRecord = AttendanceRecord::where('class_session_id', $session->id)
            ->where('student_id', $student->id)
            ->exists();

        if ($existingRecord) {
            return response()->json(['message' => 'Attendance already recorded.'], 409);
        }

        $scannedAt = now();
        $graceDeadline = $session->start_time->copy()->addMinutes($session->grace_period_minutes);

        if ($scannedAt->greaterThan($graceDeadline)) {
            return response()->json(['message' => 'The grace period has passed. You are marked absent.'], 422);
        }

        $status = $scannedAt->lessThanOrEqualTo($session->start_time)
            ? AttendanceStatus::Present
            : AttendanceStatus::Late;

        AttendanceRecord::create([
            'class_session_id' => $session->id,
            'student_id' => $student->id,
            'status' => $status,
            'scanned_at' => $scannedAt,
            'marked_by' => AttendanceMarkedBy::System,
        ]);

        $session->load('schoolClass');

        return response()->json([
            'status' => $status->value,
            'class_name' => $session->schoolClass->name,
            'session_time' => $session->start_time->format('M d, Y g:i A').' - '.$session->end_time->format('g:i A'),
        ]);
    }

    public function history(): View
    {
        $records = auth()->user()->attendanceRecords()
            ->with(['classSession.schoolClass'])
            ->orderByDesc('created_at')
            ->get();

        return view('student.attendance', compact('records'));
    }
}
