<?php

use App\Enums\ExcuseRequestStatus;
use App\Enums\SessionStatus;
use App\Models\ClassSession;
use App\Models\ExcuseRequest;
use App\Models\SchoolClass;
use App\Models\User;
use App\Notifications\AttendanceRecordedNotification;
use App\Notifications\ClassSessionStartedNotification;
use App\Notifications\ExcuseReviewedNotification;
use App\Notifications\ExcuseSubmittedNotification;
use App\Notifications\NewUserRegisteredNotification;
use App\Notifications\SessionCompletedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->teacher = User::factory()->teacher()->create();
    $this->student = User::factory()->student()->create();
    $this->class = SchoolClass::factory()->create(['teacher_id' => $this->teacher->id]);
    $this->class->students()->attach($this->student->id, ['enrolled_at' => now()]);
    $this->session = ClassSession::factory()->active()->create(['class_id' => $this->class->id]);
});

test('starting session sends notification to students', function () {
    Notification::fake();

    $scheduled = ClassSession::factory()->create([
        'class_id' => $this->class->id,
        'status' => SessionStatus::Scheduled,
    ]);

    $this->actingAs($this->teacher)->post(route('teacher.sessions.start', $scheduled));

    Notification::assertSentTo($this->student, ClassSessionStartedNotification::class);
});

test('completing session sends notification to students', function () {
    Notification::fake();

    $this->actingAs($this->teacher)->post(route('teacher.sessions.complete', $this->session));

    Notification::assertSentTo($this->student, SessionCompletedNotification::class);
});

test('scanning attendance sends notification to teacher', function () {
    Notification::fake();

    // Create session with start_time within grace period so scan succeeds
    $session = ClassSession::factory()->create([
        'class_id' => $this->class->id,
        'status' => SessionStatus::Active,
        'start_time' => now()->subMinutes(5),
        'end_time' => now()->addHours(1),
        'grace_period_minutes' => 15,
        'qr_expires_at' => now()->addHours(2),
    ]);

    $this->actingAs($this->student)->postJson(route('student.scan.store'), [
        'session_id' => $session->id,
        'token' => $session->qr_token,
    ]);

    Notification::assertSentTo($this->teacher, AttendanceRecordedNotification::class);
});

test('reviewing excuse sends notification to student', function () {
    Notification::fake();

    $excuse = ExcuseRequest::factory()->create([
        'student_id' => $this->student->id,
        'class_id' => $this->class->id,
    ]);

    $this->actingAs($this->teacher)->patch(route('teacher.excuses.review', $excuse), [
        'status' => ExcuseRequestStatus::Acknowledged->value,
    ]);

    Notification::assertSentTo($this->student, ExcuseReviewedNotification::class);
});

test('unread notifications endpoint returns json', function () {
    $this->student->notify(new SessionCompletedNotification($this->session));

    $response = $this->actingAs($this->student)->getJson(route('notifications.unread'));

    $response->assertSuccessful();
    $response->assertJsonStructure(['count', 'notifications']);
    $response->assertJsonPath('count', 1);
});

test('notification can be marked as read', function () {
    $this->student->notify(new SessionCompletedNotification($this->session));

    $notification = $this->student->unreadNotifications->first();

    $response = $this->actingAs($this->student)->postJson(route('notifications.read', $notification->id));

    $response->assertSuccessful();
    expect($this->student->fresh()->unreadNotifications)->toHaveCount(0);
});

test('all notifications can be marked as read', function () {
    $this->student->notify(new SessionCompletedNotification($this->session));
    $this->student->notify(new SessionCompletedNotification($this->session));

    $response = $this->actingAs($this->student)->postJson(route('notifications.read-all'));

    $response->assertSuccessful();
    expect($this->student->fresh()->unreadNotifications)->toHaveCount(0);
});

test('notifications index page renders', function () {
    $response = $this->actingAs($this->student)->get(route('notifications.index'));

    $response->assertSuccessful();
    $response->assertSee('Notifications');
});
