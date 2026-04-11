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
    config()->set('site.settings_file', storage_path('framework/testing/site-settings-admin.json'));
    @unlink(config('site.settings_file'));
});

test('teacher can view attendance roster for their session', function () {
    $teacher = User::factory()->teacher()->create();
    $class = SchoolClass::factory()->create(['teacher_id' => $teacher->id]);
    $student = User::factory()->student()->create();
    $class->students()->attach($student->id, ['enrolled_at' => now()]);

    $session = ClassSession::factory()->completed()->create(['class_id' => $class->id]);

    AttendanceRecord::create([
        'class_session_id' => $session->id,
        'student_id' => $student->id,
        'status' => AttendanceStatus::Present,
        'scanned_at' => now(),
        'marked_by' => AttendanceMarkedBy::System,
    ]);

    $this->actingAs($teacher)
        ->get(route('teacher.attendance.index', $session))
        ->assertOk()
        ->assertSee($student->name)
        ->assertSee('Present');
});

test('teacher can update a student status to Excused', function () {
    $teacher = User::factory()->teacher()->create();
    $class = SchoolClass::factory()->create(['teacher_id' => $teacher->id]);
    $student = User::factory()->student()->create();
    $class->students()->attach($student->id, ['enrolled_at' => now()]);

    $session = ClassSession::factory()->completed()->create(['class_id' => $class->id]);

    $record = AttendanceRecord::create([
        'class_session_id' => $session->id,
        'student_id' => $student->id,
        'status' => AttendanceStatus::Absent,
        'scanned_at' => null,
        'marked_by' => AttendanceMarkedBy::System,
    ]);

    $this->actingAs($teacher)
        ->patch(route('teacher.attendance.update', $record), [
            'status' => AttendanceStatus::Excused->value,
            'notes' => 'Medical excuse provided',
        ])
        ->assertRedirect(route('teacher.attendance.index', $session));

    $record->refresh();
    expect($record->status)->toBe(AttendanceStatus::Excused)
        ->and($record->notes)->toBe('Medical excuse provided')
        ->and($record->marked_by)->toBe(AttendanceMarkedBy::Teacher);
});

test('teacher can add notes to an attendance record', function () {
    $teacher = User::factory()->teacher()->create();
    $class = SchoolClass::factory()->create(['teacher_id' => $teacher->id]);
    $student = User::factory()->student()->create();
    $class->students()->attach($student->id, ['enrolled_at' => now()]);

    $session = ClassSession::factory()->completed()->create(['class_id' => $class->id]);

    $record = AttendanceRecord::create([
        'class_session_id' => $session->id,
        'student_id' => $student->id,
        'status' => AttendanceStatus::Present,
        'scanned_at' => now(),
        'marked_by' => AttendanceMarkedBy::System,
    ]);

    $this->actingAs($teacher)
        ->patch(route('teacher.attendance.update', $record), [
            'status' => AttendanceStatus::Present->value,
            'notes' => 'Arrived early',
        ])
        ->assertRedirect();

    expect($record->fresh()->notes)->toBe('Arrived early');
});

test('updated record shows marked_by Teacher', function () {
    $teacher = User::factory()->teacher()->create();
    $class = SchoolClass::factory()->create(['teacher_id' => $teacher->id]);
    $student = User::factory()->student()->create();
    $class->students()->attach($student->id, ['enrolled_at' => now()]);

    $session = ClassSession::factory()->completed()->create(['class_id' => $class->id]);

    $record = AttendanceRecord::create([
        'class_session_id' => $session->id,
        'student_id' => $student->id,
        'status' => AttendanceStatus::Late,
        'scanned_at' => now(),
        'marked_by' => AttendanceMarkedBy::System,
    ]);

    $this->actingAs($teacher)
        ->patch(route('teacher.attendance.update', $record), [
            'status' => AttendanceStatus::Present->value,
        ]);

    expect($record->fresh()->marked_by)->toBe(AttendanceMarkedBy::Teacher);
});

test('teacher cannot edit attendance for another teachers session', function () {
    $teacher = User::factory()->teacher()->create();
    $otherTeacher = User::factory()->teacher()->create();
    $class = SchoolClass::factory()->create(['teacher_id' => $teacher->id]);
    $student = User::factory()->student()->create();
    $class->students()->attach($student->id, ['enrolled_at' => now()]);

    $session = ClassSession::factory()->completed()->create(['class_id' => $class->id]);

    $record = AttendanceRecord::create([
        'class_session_id' => $session->id,
        'student_id' => $student->id,
        'status' => AttendanceStatus::Present,
        'scanned_at' => now(),
        'marked_by' => AttendanceMarkedBy::System,
    ]);

    $this->actingAs($otherTeacher)
        ->patch(route('teacher.attendance.update', $record), [
            'status' => AttendanceStatus::Excused->value,
        ])
        ->assertForbidden();
});

test('CSV export contains correct data', function () {
    $teacher = User::factory()->teacher()->create();
    $class = SchoolClass::factory()->create(['teacher_id' => $teacher->id, 'name' => 'Test Class']);
    $student = User::factory()->student()->create(['name' => 'John Doe', 'email' => 'john@example.com']);
    $class->students()->attach($student->id, ['enrolled_at' => now()]);

    $session = ClassSession::factory()->completed()->create(['class_id' => $class->id]);

    AttendanceRecord::create([
        'class_session_id' => $session->id,
        'student_id' => $student->id,
        'status' => AttendanceStatus::Present,
        'scanned_at' => now(),
        'marked_by' => AttendanceMarkedBy::System,
        'notes' => 'On time',
    ]);

    $response = $this->actingAs($teacher)
        ->get(route('teacher.attendance.export', $session));

    $response->assertOk()
        ->assertHeader('content-type', 'text/csv; charset=UTF-8');

    $content = $response->streamedContent();
    expect($content)->toContain('"Student Name"')
        ->and($content)->toContain('John Doe')
        ->and($content)->toContain('john@example.com')
        ->and($content)->toContain('Present')
        ->and($content)->toContain('On time');
});

test('CSV export has proper headers', function () {
    $teacher = User::factory()->teacher()->create();
    $class = SchoolClass::factory()->create(['teacher_id' => $teacher->id]);
    $session = ClassSession::factory()->completed()->create(['class_id' => $class->id]);

    $response = $this->actingAs($teacher)
        ->get(route('teacher.attendance.export', $session));

    $response->assertOk()
        ->assertDownload();
});
