<?php

use App\Enums\AttendanceMarkedBy;
use App\Enums\AttendanceStatus;
use App\Enums\ExcuseRequestStatus;
use App\Enums\SessionStatus;
use App\Jobs\MarkAbsenteesAfterSession;
use App\Models\ClassSession;
use App\Models\ExcuseRequest;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    config(['site.timezone' => 'Asia/Manila']);
    $this->student = User::factory()->student()->create();
    $this->teacher = User::factory()->teacher()->create();
    $this->class = SchoolClass::factory()->create(['teacher_id' => $this->teacher->id]);
    $this->class->students()->attach($this->student->id, ['enrolled_at' => now()]);
});

// ─── Student Excuse Request Submission ─────────────────────────────────

test('student can view excuse requests page', function () {
    $response = $this->actingAs($this->student)->get(route('student.excuses.index'));

    $response->assertSuccessful();
    $response->assertSee('Excuse Requests');
});

test('student can view create excuse request form', function () {
    $response = $this->actingAs($this->student)->get(route('student.excuses.create'));

    $response->assertSuccessful();
    $response->assertSee('Submit Excuse Request');
});

test('student can submit an excuse request', function () {
    Storage::fake('local');

    $response = $this->actingAs($this->student)->post(route('student.excuses.store'), [
        'class_id' => $this->class->id,
        'excuse_date' => now()->addDays(3)->format('Y-m-d'),
        'reason' => 'Family event',
        'document' => UploadedFile::fake()->create('letter.pdf', 100, 'application/pdf'),
    ]);

    $response->assertRedirect(route('student.excuses.index'));

    $this->assertDatabaseHas('excuse_requests', [
        'student_id' => $this->student->id,
        'class_id' => $this->class->id,
        'reason' => 'Family event',
        'status' => ExcuseRequestStatus::Pending->value,
    ]);

    Storage::disk('local')->assertExists(
        ExcuseRequest::where('student_id', $this->student->id)->first()->document_path
    );
});

test('excuse request requires a pdf document', function () {
    Storage::fake('local');

    $response = $this->actingAs($this->student)->post(route('student.excuses.store'), [
        'class_id' => $this->class->id,
        'excuse_date' => now()->addDays(3)->format('Y-m-d'),
        'reason' => 'Family event',
        'document' => UploadedFile::fake()->create('letter.jpg', 100, 'image/jpeg'),
    ]);

    $response->assertSessionHasErrors('document');
});

test('excuse request date must be in the future', function () {
    Storage::fake('local');

    $response = $this->actingAs($this->student)->post(route('student.excuses.store'), [
        'class_id' => $this->class->id,
        'excuse_date' => now()->subDay()->format('Y-m-d'),
        'reason' => 'Family event',
        'document' => UploadedFile::fake()->create('letter.pdf', 100, 'application/pdf'),
    ]);

    $response->assertSessionHasErrors('excuse_date');
});

test('student can see their submitted excuse requests', function () {
    ExcuseRequest::factory()->create([
        'student_id' => $this->student->id,
        'class_id' => $this->class->id,
        'reason' => 'Medical appointment',
        'status' => ExcuseRequestStatus::Pending,
    ]);

    $response = $this->actingAs($this->student)->get(route('student.excuses.index'));

    $response->assertSuccessful();
    $response->assertSee('Medical appointment');
    $response->assertSee('Pending');
});

// ─── Teacher Excuse Request Management ─────────────────────────────────

test('teacher can view excuse requests from their students', function () {
    ExcuseRequest::factory()->create([
        'student_id' => $this->student->id,
        'class_id' => $this->class->id,
        'reason' => 'Doctor visit',
        'status' => ExcuseRequestStatus::Pending,
    ]);

    $response = $this->actingAs($this->teacher)->get(route('teacher.excuses.index'));

    $response->assertSuccessful();
    $response->assertSee($this->student->name);
    $response->assertSee('Doctor visit');
});

test('teacher does not see excuse requests from students not in their classes', function () {
    $otherClass = SchoolClass::factory()->create();
    $otherStudent = User::factory()->student()->create();
    ExcuseRequest::factory()->create([
        'student_id' => $otherStudent->id,
        'class_id' => $otherClass->id,
        'reason' => 'Unrelated excuse',
    ]);

    $response = $this->actingAs($this->teacher)->get(route('teacher.excuses.index'));

    $response->assertSuccessful();
    $response->assertDontSee('Unrelated excuse');
});

test('teacher can acknowledge an excuse request', function () {
    $excuseRequest = ExcuseRequest::factory()->create([
        'student_id' => $this->student->id,
        'class_id' => $this->class->id,
        'status' => ExcuseRequestStatus::Pending,
    ]);

    $response = $this->actingAs($this->teacher)->patch(route('teacher.excuses.review', $excuseRequest), [
        'status' => 'Acknowledged',
        'reviewer_notes' => 'Approved',
    ]);

    $response->assertRedirect(route('teacher.excuses.index'));

    $excuseRequest->refresh();
    expect($excuseRequest->status)->toBe(ExcuseRequestStatus::Acknowledged);
    expect($excuseRequest->reviewed_by)->toBe($this->teacher->id);
    expect($excuseRequest->reviewer_notes)->toBe('Approved');
});

test('teacher can reject an excuse request', function () {
    $excuseRequest = ExcuseRequest::factory()->create([
        'student_id' => $this->student->id,
        'class_id' => $this->class->id,
        'status' => ExcuseRequestStatus::Pending,
    ]);

    $response = $this->actingAs($this->teacher)->patch(route('teacher.excuses.review', $excuseRequest), [
        'status' => 'Rejected',
        'reviewer_notes' => 'Insufficient documentation',
    ]);

    $response->assertRedirect(route('teacher.excuses.index'));

    $excuseRequest->refresh();
    expect($excuseRequest->status)->toBe(ExcuseRequestStatus::Rejected);
});

test('teacher cannot review excuse request for student not in their class', function () {
    $otherClass = SchoolClass::factory()->create();
    $otherStudent = User::factory()->student()->create();
    $excuseRequest = ExcuseRequest::factory()->create([
        'student_id' => $otherStudent->id,
        'class_id' => $otherClass->id,
        'status' => ExcuseRequestStatus::Pending,
    ]);

    $response = $this->actingAs($this->teacher)->patch(route('teacher.excuses.review', $excuseRequest), [
        'status' => 'Acknowledged',
    ]);

    $response->assertForbidden();
});

// ─── Auto-Excuse on Session Complete ───────────────────────────────────

test('acknowledged excuse request auto-excuses student on session complete', function () {
    $sessionDate = now()->addDays(3);

    ExcuseRequest::factory()->acknowledged()->create([
        'student_id' => $this->student->id,
        'class_id' => $this->class->id,
        'excuse_date' => $sessionDate->toDateString(),
        'reviewed_by' => $this->teacher->id,
    ]);

    $session = ClassSession::factory()->create([
        'class_id' => $this->class->id,
        'start_time' => $sessionDate->copy()->setTime(10, 0),
        'end_time' => $sessionDate->copy()->setTime(12, 0),
        'status' => SessionStatus::Active,
    ]);

    (new MarkAbsenteesAfterSession($session))->handle();

    $record = $session->attendanceRecords()->where('student_id', $this->student->id)->first();
    expect($record)->not->toBeNull();
    expect($record->status)->toBe(AttendanceStatus::Excused);
    expect($record->notes)->toContain('Auto-excused');
});

test('rejected excuse request does not auto-excuse student', function () {
    $sessionDate = now()->addDays(3);

    ExcuseRequest::factory()->rejected()->create([
        'student_id' => $this->student->id,
        'class_id' => $this->class->id,
        'excuse_date' => $sessionDate->toDateString(),
        'reviewed_by' => $this->teacher->id,
    ]);

    $session = ClassSession::factory()->create([
        'class_id' => $this->class->id,
        'start_time' => $sessionDate->copy()->setTime(10, 0),
        'end_time' => $sessionDate->copy()->setTime(12, 0),
        'status' => SessionStatus::Active,
    ]);

    (new MarkAbsenteesAfterSession($session))->handle();

    $record = $session->attendanceRecords()->where('student_id', $this->student->id)->first();
    expect($record)->not->toBeNull();
    expect($record->status)->toBe(AttendanceStatus::Absent);
});

test('excuse request for a different class does not auto-excuse student', function () {
    $sessionDate = now()->addDays(3);
    $otherClass = SchoolClass::factory()->create(['teacher_id' => $this->teacher->id]);
    $otherClass->students()->attach($this->student->id, ['enrolled_at' => now()]);

    ExcuseRequest::factory()->acknowledged()->create([
        'student_id' => $this->student->id,
        'class_id' => $otherClass->id,
        'excuse_date' => $sessionDate->toDateString(),
        'reviewed_by' => $this->teacher->id,
    ]);

    $session = ClassSession::factory()->create([
        'class_id' => $this->class->id,
        'start_time' => $sessionDate->copy()->setTime(10, 0),
        'end_time' => $sessionDate->copy()->setTime(12, 0),
        'status' => SessionStatus::Active,
    ]);

    (new MarkAbsenteesAfterSession($session))->handle();

    $record = $session->attendanceRecords()->where('student_id', $this->student->id)->first();
    expect($record)->not->toBeNull();
    expect($record->status)->toBe(AttendanceStatus::Absent);
});

test('student cannot submit excuse request for a class they are not enrolled in', function () {
    Storage::fake('local');
    $otherClass = SchoolClass::factory()->create();

    $response = $this->actingAs($this->student)->post(route('student.excuses.store'), [
        'class_id' => $otherClass->id,
        'excuse_date' => now()->addDays(3)->format('Y-m-d'),
        'reason' => 'Family event',
        'document' => UploadedFile::fake()->create('letter.pdf', 100, 'application/pdf'),
    ]);

    $response->assertSessionHasErrors('class_id');
});
