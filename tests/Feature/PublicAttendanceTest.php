<?php

use App\Enums\AttendanceStatus;
use App\Enums\SessionStatus;
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
    $this->session = ClassSession::factory()->active()->create([
        'class_id' => $this->class->id,
        'start_time' => now()->subMinutes(5),
        'grace_period_minutes' => 15,
    ]);
});

test('public attendance page renders for valid session', function () {
    $response = $this->get(route('attend.show', [$this->session->id, $this->session->qr_token]));

    $response->assertSuccessful();
    $response->assertSee('Mark Attendance');
});

test('public attendance page shows error for invalid token', function () {
    $response = $this->get(route('attend.show', [$this->session->id, 'wrong-token']));

    $response->assertSuccessful();
    $response->assertSee('Invalid attendance link');
});

test('public attendance page shows error for inactive session', function () {
    $this->session->update(['status' => SessionStatus::Completed]);

    $response = $this->get(route('attend.show', [$this->session->id, $this->session->qr_token]));

    $response->assertSuccessful();
    $response->assertSee('not currently active');
});

test('student can record attendance via public link', function () {
    $response = $this->post(route('attend.store', [$this->session->id, $this->session->qr_token]), [
        'email' => $this->student->email,
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('attendance_success');

    expect(AttendanceRecord::where('student_id', $this->student->id)->exists())->toBeTrue();
});

test('student cannot record attendance twice via public link', function () {
    AttendanceRecord::create([
        'class_session_id' => $this->session->id,
        'student_id' => $this->student->id,
        'status' => AttendanceStatus::Present,
        'scanned_at' => now(),
        'marked_by' => 'System',
    ]);

    $response = $this->post(route('attend.store', [$this->session->id, $this->session->qr_token]), [
        'email' => $this->student->email,
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('attendance_success');
});

test('non-enrolled student cannot record attendance via public link', function () {
    $otherStudent = User::factory()->student()->create();

    $response = $this->post(route('attend.store', [$this->session->id, $this->session->qr_token]), [
        'email' => $otherStudent->email,
    ]);

    $response->assertRedirect();
});

test('invalid email is rejected on public attendance form', function () {
    $response = $this->post(route('attend.store', [$this->session->id, $this->session->qr_token]), [
        'email' => 'nonexistent@example.com',
    ]);

    $response->assertRedirect();
});

test('qr code is displayed on session show page', function () {
    $response = $this->actingAs($this->teacher)->get(route('teacher.sessions.show', $this->session));

    $response->assertSuccessful();
    $response->assertSee('QR Code', false);
});
