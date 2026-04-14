<?php

use App\Enums\AttendanceMarkedBy;
use App\Enums\AttendanceStatus;
use App\Models\AttendanceRecord;
use App\Models\ClassSession;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->teacher = User::factory()->teacher()->create();
    $this->student = User::factory()->student()->create();
    $this->class = SchoolClass::factory()->create(['teacher_id' => $this->teacher->id]);
    $this->class->students()->attach($this->student->id, ['enrolled_at' => now()]);
});

test('student can view calendar page with no records', function () {
    $response = $this->actingAs($this->student)->get(route('student.calendar.index'));

    $response->assertSuccessful();
    $response->assertSee('Attendance Calendar');
});

test('student can view calendar page with past attendance records', function () {
    $pastSession = ClassSession::factory()->create([
        'class_id' => $this->class->id,
        'start_time' => now()->subDays(5)->setTime(9, 0),
        'end_time' => now()->subDays(5)->setTime(11, 0),
        'status' => 'Completed',
    ]);

    AttendanceRecord::create([
        'class_session_id' => $pastSession->id,
        'student_id' => $this->student->id,
        'status' => AttendanceStatus::Present,
        'scanned_at' => now()->subDays(5)->setTime(9, 5),
        'marked_by' => AttendanceMarkedBy::System,
    ]);

    $response = $this->actingAs($this->student)->get(route('student.calendar.index'));

    $response->assertSuccessful();
    $response->assertSee('Attendance Calendar');
});

test('calendar handles records with deleted sessions gracefully', function () {
    $session = ClassSession::factory()->create([
        'class_id' => $this->class->id,
        'start_time' => now()->subDays(3)->setTime(9, 0),
        'end_time' => now()->subDays(3)->setTime(11, 0),
        'status' => 'Completed',
    ]);

    $record = AttendanceRecord::create([
        'class_session_id' => $session->id,
        'student_id' => $this->student->id,
        'status' => AttendanceStatus::Present,
        'scanned_at' => now()->subDays(3)->setTime(9, 5),
        'marked_by' => AttendanceMarkedBy::System,
    ]);

    // Delete the session — record remains orphaned
    $session->delete();

    $response = $this->actingAs($this->student)->get(route('student.calendar.index'));

    $response->assertSuccessful();
});

test('calendar handles multiple months of attendance', function () {
    foreach ([30, 15, 5] as $daysAgo) {
        $session = ClassSession::factory()->create([
            'class_id' => $this->class->id,
            'start_time' => now()->subDays($daysAgo)->setTime(9, 0),
            'end_time' => now()->subDays($daysAgo)->setTime(11, 0),
            'status' => 'Completed',
        ]);

        AttendanceRecord::create([
            'class_session_id' => $session->id,
            'student_id' => $this->student->id,
            'status' => AttendanceStatus::Present,
            'scanned_at' => now()->subDays($daysAgo)->setTime(9, 5),
            'marked_by' => AttendanceMarkedBy::System,
        ]);
    }

    $response = $this->actingAs($this->student)->get(route('student.calendar.index'));

    $response->assertSuccessful();
});
