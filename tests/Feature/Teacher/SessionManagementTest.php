<?php

use App\Enums\AttendanceMarkedBy;
use App\Enums\AttendanceStatus;
use App\Enums\SessionModality;
use App\Enums\SessionStatus;
use App\Jobs\MarkAbsenteesAfterSession;
use App\Models\AttendanceRecord;
use App\Models\ClassSession;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

beforeEach(function () {
    config()->set('site.settings_file', storage_path('framework/testing/site-settings-admin.json'));
    @unlink(config('site.settings_file'));
});

test('teacher can create a session for their class', function () {
    $teacher = User::factory()->teacher()->create();
    $class = SchoolClass::factory()->create(['teacher_id' => $teacher->id]);

    $startTime = now()->addDay()->setTime(10, 0)->format('Y-m-d H:i:s');
    $endTime = now()->addDay()->setTime(12, 0)->format('Y-m-d H:i:s');

    $this->actingAs($teacher)
        ->post(route('teacher.sessions.store', $class), [
            'modality' => SessionModality::Onsite->value,
            'location' => 'Room 101',
            'start_time' => $startTime,
            'end_time' => $endTime,
            'grace_period_minutes' => 15,
        ])
        ->assertRedirect();

    $session = ClassSession::where('class_id', $class->id)->first();

    expect($session)->not->toBeNull()
        ->and($session->modality)->toBe(SessionModality::Onsite)
        ->and($session->location)->toBe('Room 101')
        ->and($session->status)->toBe(SessionStatus::Scheduled)
        ->and($session->qr_token)->toHaveLength(64);
});

test('teacher can start a session', function () {
    $teacher = User::factory()->teacher()->create();
    $class = SchoolClass::factory()->create(['teacher_id' => $teacher->id]);
    $session = ClassSession::factory()->create(['class_id' => $class->id]);

    $this->actingAs($teacher)
        ->post(route('teacher.sessions.start', $session))
        ->assertRedirect();

    expect($session->fresh()->status)->toBe(SessionStatus::Active);
});

test('teacher can complete a session', function () {
    Queue::fake();

    $teacher = User::factory()->teacher()->create();
    $class = SchoolClass::factory()->create(['teacher_id' => $teacher->id]);
    $session = ClassSession::factory()->active()->create(['class_id' => $class->id]);

    $this->actingAs($teacher)
        ->post(route('teacher.sessions.complete', $session))
        ->assertRedirect();

    expect($session->fresh()->status)->toBe(SessionStatus::Completed);

    Queue::assertPushed(MarkAbsenteesAfterSession::class, function ($job) use ($session) {
        return $job->session->id === $session->id;
    });
});

test('teacher can cancel a session', function () {
    $teacher = User::factory()->teacher()->create();
    $class = SchoolClass::factory()->create(['teacher_id' => $teacher->id]);
    $session = ClassSession::factory()->create(['class_id' => $class->id]);

    $this->actingAs($teacher)
        ->post(route('teacher.sessions.cancel', $session))
        ->assertRedirect();

    expect($session->fresh()->status)->toBe(SessionStatus::Cancelled);
});

test('teacher cannot manage another teachers session', function () {
    $teacher = User::factory()->teacher()->create();
    $otherTeacher = User::factory()->teacher()->create();
    $class = SchoolClass::factory()->create(['teacher_id' => $otherTeacher->id]);
    $session = ClassSession::factory()->create(['class_id' => $class->id]);

    $this->actingAs($teacher)
        ->get(route('teacher.sessions.show', $session))
        ->assertForbidden();

    $this->actingAs($teacher)
        ->post(route('teacher.sessions.start', $session))
        ->assertForbidden();

    $this->actingAs($teacher)
        ->post(route('teacher.sessions.complete', $session))
        ->assertForbidden();

    $this->actingAs($teacher)
        ->post(route('teacher.sessions.cancel', $session))
        ->assertForbidden();
});

test('student can scan valid qr and get present status', function () {
    $teacher = User::factory()->teacher()->create();
    $class = SchoolClass::factory()->create(['teacher_id' => $teacher->id]);
    $student = User::factory()->student()->create();
    $class->students()->attach($student->id, ['enrolled_at' => now()]);

    // Start time is in the future, so scanning now = before start = Present
    $session = ClassSession::factory()->create([
        'class_id' => $class->id,
        'status' => SessionStatus::Active,
        'start_time' => now()->addMinutes(10),
        'end_time' => now()->addHours(2),
        'grace_period_minutes' => 15,
    ]);

    $this->actingAs($student)
        ->postJson(route('student.scan.store'), [
            'session_id' => $session->id,
            'token' => $session->qr_token,
        ])
        ->assertSuccessful()
        ->assertJson(['status' => 'Present']);

    $record = AttendanceRecord::where('class_session_id', $session->id)
        ->where('student_id', $student->id)
        ->first();

    expect($record)->not->toBeNull()
        ->and($record->status)->toBe(AttendanceStatus::Present)
        ->and($record->marked_by)->toBe(AttendanceMarkedBy::System);
});

test('student scanning after start but within grace period gets late status', function () {
    $teacher = User::factory()->teacher()->create();
    $class = SchoolClass::factory()->create(['teacher_id' => $teacher->id]);
    $student = User::factory()->student()->create();
    $class->students()->attach($student->id, ['enrolled_at' => now()]);

    // Start time is 5 min ago, grace period is 30 min — so scanning now = Late (after start, within grace)
    $session = ClassSession::factory()->create([
        'class_id' => $class->id,
        'status' => SessionStatus::Active,
        'start_time' => now()->subMinutes(5),
        'end_time' => now()->addHours(2),
        'grace_period_minutes' => 30,
    ]);

    $this->actingAs($student)
        ->postJson(route('student.scan.store'), [
            'session_id' => $session->id,
            'token' => $session->qr_token,
        ])
        ->assertSuccessful()
        ->assertJson(['status' => 'Late']);

    $record = AttendanceRecord::where('class_session_id', $session->id)
        ->where('student_id', $student->id)
        ->first();

    expect($record->status)->toBe(AttendanceStatus::Late);
});

test('student scanning after grace period is rejected', function () {
    $teacher = User::factory()->teacher()->create();
    $class = SchoolClass::factory()->create(['teacher_id' => $teacher->id]);
    $student = User::factory()->student()->create();
    $class->students()->attach($student->id, ['enrolled_at' => now()]);

    // Start time 1 hour ago, grace period 15 min — grace deadline long passed
    $session = ClassSession::factory()->create([
        'class_id' => $class->id,
        'status' => SessionStatus::Active,
        'start_time' => now()->subHour(),
        'end_time' => now()->addHour(),
        'grace_period_minutes' => 15,
    ]);

    $this->actingAs($student)
        ->postJson(route('student.scan.store'), [
            'session_id' => $session->id,
            'token' => $session->qr_token,
        ])
        ->assertStatus(422)
        ->assertJson(['message' => 'The grace period has passed. You are marked absent.']);

    expect(AttendanceRecord::where('class_session_id', $session->id)
        ->where('student_id', $student->id)
        ->exists())->toBeFalse();
});

test('student cannot scan for a non-active session', function () {
    $teacher = User::factory()->teacher()->create();
    $class = SchoolClass::factory()->create(['teacher_id' => $teacher->id]);
    $student = User::factory()->student()->create();
    $class->students()->attach($student->id, ['enrolled_at' => now()]);

    $session = ClassSession::factory()->create(['class_id' => $class->id]);

    $this->actingAs($student)
        ->postJson(route('student.scan.store'), [
            'session_id' => $session->id,
            'token' => $session->qr_token,
        ])
        ->assertStatus(422)
        ->assertJson(['message' => 'This session is not currently active.']);
});

test('student cannot scan if not enrolled', function () {
    $teacher = User::factory()->teacher()->create();
    $class = SchoolClass::factory()->create(['teacher_id' => $teacher->id]);
    $student = User::factory()->student()->create();

    $session = ClassSession::factory()->active()->create(['class_id' => $class->id]);

    $this->actingAs($student)
        ->postJson(route('student.scan.store'), [
            'session_id' => $session->id,
            'token' => $session->qr_token,
        ])
        ->assertForbidden()
        ->assertJson(['message' => 'You are not enrolled in this class.']);
});

test('student cannot scan twice for same session', function () {
    $teacher = User::factory()->teacher()->create();
    $class = SchoolClass::factory()->create(['teacher_id' => $teacher->id]);
    $student = User::factory()->student()->create();
    $class->students()->attach($student->id, ['enrolled_at' => now()]);

    $session = ClassSession::factory()->create([
        'class_id' => $class->id,
        'status' => SessionStatus::Active,
        'start_time' => now()->addMinutes(10),
        'end_time' => now()->addHours(2),
        'grace_period_minutes' => 15,
    ]);

    AttendanceRecord::create([
        'class_session_id' => $session->id,
        'student_id' => $student->id,
        'status' => AttendanceStatus::Present,
        'scanned_at' => now(),
        'marked_by' => AttendanceMarkedBy::System,
    ]);

    $this->actingAs($student)
        ->postJson(route('student.scan.store'), [
            'session_id' => $session->id,
            'token' => $session->qr_token,
        ])
        ->assertStatus(409)
        ->assertJson(['message' => 'Attendance already recorded.']);
});

test('invalid qr token is rejected', function () {
    $teacher = User::factory()->teacher()->create();
    $class = SchoolClass::factory()->create(['teacher_id' => $teacher->id]);
    $student = User::factory()->student()->create();
    $class->students()->attach($student->id, ['enrolled_at' => now()]);

    $session = ClassSession::factory()->active()->create(['class_id' => $class->id]);

    $this->actingAs($student)
        ->postJson(route('student.scan.store'), [
            'session_id' => $session->id,
            'token' => 'invalid-token',
        ])
        ->assertStatus(422)
        ->assertJson(['message' => 'Invalid QR code.']);
});

test('mark absentees after session job creates absent records', function () {
    $teacher = User::factory()->teacher()->create();
    $class = SchoolClass::factory()->create(['teacher_id' => $teacher->id]);
    $student1 = User::factory()->student()->create();
    $student2 = User::factory()->student()->create();
    $student3 = User::factory()->student()->create();

    $class->students()->attach([
        $student1->id => ['enrolled_at' => now()],
        $student2->id => ['enrolled_at' => now()],
        $student3->id => ['enrolled_at' => now()],
    ]);

    $session = ClassSession::factory()->completed()->create(['class_id' => $class->id]);

    // Student 1 scanned (has a record)
    AttendanceRecord::create([
        'class_session_id' => $session->id,
        'student_id' => $student1->id,
        'status' => AttendanceStatus::Present,
        'scanned_at' => now(),
        'marked_by' => AttendanceMarkedBy::System,
    ]);

    // Run the job
    (new MarkAbsenteesAfterSession($session))->handle();

    // Student 2 and 3 should be marked absent
    $absentRecords = AttendanceRecord::where('class_session_id', $session->id)
        ->where('status', AttendanceStatus::Absent)
        ->get();

    expect($absentRecords)->toHaveCount(2);

    foreach ($absentRecords as $record) {
        expect($record->marked_by)->toBe(AttendanceMarkedBy::System)
            ->and($record->scanned_at)->toBeNull();
    }

    // Total records should be 3
    expect(AttendanceRecord::where('class_session_id', $session->id)->count())->toBe(3);
});

test('student can view their attendance history', function () {
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

    $this->actingAs($student)
        ->get(route('student.attendance.index'))
        ->assertOk()
        ->assertSee($class->name)
        ->assertSee('Present');
});

test('teacher can poll attendance data as JSON for active session', function () {
    $teacher = User::factory()->teacher()->create();
    $class = SchoolClass::factory()->create(['teacher_id' => $teacher->id]);
    $student = User::factory()->student()->create();
    $class->students()->attach($student->id, ['enrolled_at' => now()]);

    $session = ClassSession::factory()->active()->create(['class_id' => $class->id]);

    AttendanceRecord::create([
        'class_session_id' => $session->id,
        'student_id' => $student->id,
        'status' => AttendanceStatus::Present,
        'scanned_at' => now(),
        'marked_by' => AttendanceMarkedBy::System,
    ]);

    $this->actingAs($teacher)
        ->getJson(route('teacher.sessions.attendance', $session))
        ->assertOk()
        ->assertJsonStructure(['scanned_count', 'records', 'session_status'])
        ->assertJsonPath('scanned_count', 1)
        ->assertJsonPath('session_status', 'Active')
        ->assertJsonPath('records.0.student_name', $student->name)
        ->assertJsonPath('records.0.status', 'Present');
});

test('non-owner teacher cannot poll attendance data', function () {
    $teacher = User::factory()->teacher()->create();
    $otherTeacher = User::factory()->teacher()->create();
    $class = SchoolClass::factory()->create(['teacher_id' => $teacher->id]);
    $session = ClassSession::factory()->active()->create(['class_id' => $class->id]);

    $this->actingAs($otherTeacher)
        ->getJson(route('teacher.sessions.attendance', $session))
        ->assertForbidden();
});

test('teacher can bulk schedule sessions for selected days', function () {
    $teacher = User::factory()->teacher()->create();
    $class = SchoolClass::factory()->create(['teacher_id' => $teacher->id]);

    // Schedule Monday and Wednesday sessions for the next 4 weeks
    $endDate = now()->addWeeks(4)->format('Y-m-d');

    $this->actingAs($teacher)
        ->post(route('teacher.sessions.bulk-store', $class), [
            'days' => ['Monday', 'Wednesday'],
            'modality' => SessionModality::Onsite->value,
            'location' => 'Room 202',
            'start_time' => '09:00',
            'end_time' => '11:00',
            'grace_period_minutes' => 10,
            'interval_weeks' => 1,
            'start_date' => now()->format('Y-m-d'),
            'end_date' => $endDate,
        ])
        ->assertRedirect(route('teacher.classes.show', $class));

    $sessions = ClassSession::where('class_id', $class->id)->get();

    expect($sessions->count())->toBeGreaterThanOrEqual(4)
        ->and($sessions->first()->modality)->toBe(SessionModality::Onsite)
        ->and($sessions->first()->location)->toBe('Room 202')
        ->and($sessions->first()->grace_period_minutes)->toBe(10)
        ->and($sessions->first()->recurrence_group_id)->not->toBeNull()
        ->and($sessions->pluck('recurrence_group_id')->unique())->toHaveCount(1);

    // All sessions should be on Monday (1) or Wednesday (3)
    $sessions->each(function ($session) {
        expect($session->start_time->dayOfWeekIso)->toBeIn([1, 3]);
    });
});

test('teacher can bulk schedule with biweekly interval', function () {
    $teacher = User::factory()->teacher()->create();
    $class = SchoolClass::factory()->create(['teacher_id' => $teacher->id]);

    $endDate = now()->addWeeks(6)->format('Y-m-d');

    $this->actingAs($teacher)
        ->post(route('teacher.sessions.bulk-store', $class), [
            'days' => ['Friday'],
            'modality' => SessionModality::Online->value,
            'location' => 'https://meet.example.com/abc',
            'start_time' => '14:00',
            'end_time' => '16:00',
            'grace_period_minutes' => 15,
            'interval_weeks' => 2,
            'start_date' => now()->format('Y-m-d'),
            'end_date' => $endDate,
        ])
        ->assertRedirect(route('teacher.classes.show', $class));

    $sessions = ClassSession::where('class_id', $class->id)->get();

    // With biweekly interval over 6 weeks, expect ~3 Friday sessions
    expect($sessions->count())->toBeGreaterThanOrEqual(2)
        ->and($sessions->count())->toBeLessThanOrEqual(4);

    $sessions->each(function ($session) {
        expect($session->start_time->dayOfWeekIso)->toBe(5)
            ->and($session->modality)->toBe(SessionModality::Online);
    });
});

test('bulk schedule requires at least one day selected', function () {
    $teacher = User::factory()->teacher()->create();
    $class = SchoolClass::factory()->create(['teacher_id' => $teacher->id]);

    $this->actingAs($teacher)
        ->post(route('teacher.sessions.bulk-store', $class), [
            'days' => [],
            'modality' => SessionModality::Onsite->value,
            'start_time' => '09:00',
            'end_time' => '11:00',
            'interval_weeks' => 1,
            'start_date' => now()->format('Y-m-d'),
            'end_date' => now()->addWeeks(4)->format('Y-m-d'),
        ])
        ->assertSessionHasErrors('days', errorBag: 'preschedule');
});

test('bulk schedule validates end date is in the future', function () {
    $teacher = User::factory()->teacher()->create();
    $class = SchoolClass::factory()->create(['teacher_id' => $teacher->id]);

    $this->actingAs($teacher)
        ->post(route('teacher.sessions.bulk-store', $class), [
            'days' => ['Monday'],
            'modality' => SessionModality::Onsite->value,
            'start_time' => '09:00',
            'end_time' => '11:00',
            'interval_weeks' => 1,
            'start_date' => now()->format('Y-m-d'),
            'end_date' => now()->subDay()->format('Y-m-d'),
        ])
        ->assertSessionHasErrors('end_date', errorBag: 'preschedule');
});
