<?php

use App\Console\Commands\SendWeeklyReports;
use App\Enums\AttendanceStatus;
use App\Enums\ClassStatus;
use App\Models\AttendanceRecord;
use App\Models\ClassSession;
use App\Models\SchoolClass;
use App\Models\User;
use App\Notifications\ClassSessionStartedNotification;
use App\Notifications\WeeklyAttendanceSummaryNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

beforeEach(function () {
    config()->set('site.settings_file', storage_path('framework/testing/site-settings-phase8.json'));
    @unlink(config('site.settings_file'));
});

// ─── ClassSessionStartedNotification ─────────────────────────────────────────

test('ClassSessionStartedNotification is dispatched to enrolled students when session starts', function () {
    Notification::fake();

    $teacher = User::factory()->teacher()->create();
    $student = User::factory()->student()->create();
    $class = SchoolClass::factory()->create(['teacher_id' => $teacher->id]);
    $class->students()->attach($student->id, ['enrolled_at' => now()]);
    $session = ClassSession::factory()->create(['class_id' => $class->id]);

    $this->actingAs($teacher)
        ->post(route('teacher.sessions.start', $session))
        ->assertRedirect();

    Notification::assertSentTo($student, ClassSessionStartedNotification::class);
});

test('ClassSessionStartedNotification is NOT sent to non-enrolled students', function () {
    Notification::fake();

    $teacher = User::factory()->teacher()->create();
    $enrolled = User::factory()->student()->create();
    $notEnrolled = User::factory()->student()->create();
    $class = SchoolClass::factory()->create(['teacher_id' => $teacher->id]);
    $class->students()->attach($enrolled->id, ['enrolled_at' => now()]);
    $session = ClassSession::factory()->create(['class_id' => $class->id]);

    $this->actingAs($teacher)
        ->post(route('teacher.sessions.start', $session))
        ->assertRedirect();

    Notification::assertSentTo($enrolled, ClassSessionStartedNotification::class);
    Notification::assertNotSentTo($notEnrolled, ClassSessionStartedNotification::class);
});

test('ClassSessionStartedNotification email contains class name, time, and attendance reminder', function () {
    $teacher = User::factory()->teacher()->create();
    $student = User::factory()->student()->create();
    $class = SchoolClass::factory()->create(['teacher_id' => $teacher->id, 'name' => 'Biology 101']);
    $session = ClassSession::factory()->active()->create(['class_id' => $class->id]);

    $notification = new ClassSessionStartedNotification($session);
    $mail = $notification->toMail($student);
    $rendered = $mail->render();

    expect($rendered)
        ->toContain('Biology 101')
        ->toContain($session->start_time->format('g:i A'))
        ->toContain('QR code');
});

// ─── WeeklyAttendanceSummaryNotification ──────────────────────────────────────

test('WeeklyAttendanceSummaryNotification contains correct stats for student', function () {
    $student = User::factory()->student()->create();

    $data = [
        'present' => 3,
        'late'    => 1,
        'absent'  => 1,
        'excused' => 0,
        'total'   => 5,
        'rate'    => 80.0,
        'classes' => [['name' => 'Math', 'sessions' => 5, 'status' => 'present']],
    ];

    $notification = new WeeklyAttendanceSummaryNotification('student', $data);
    $mail = $notification->toMail($student);
    $rendered = $mail->render();

    expect($rendered)
        ->toContain('3')
        ->toContain('80')
        ->toContain('Math');
});

test('WeeklyAttendanceSummaryNotification contains correct stats for teacher', function () {
    $teacher = User::factory()->teacher()->create();

    $data = [
        'classes' => [
            ['name' => 'Physics', 'sessions' => 4, 'rate' => 75.0, 'absences' => 2],
        ],
    ];

    $notification = new WeeklyAttendanceSummaryNotification('teacher', $data);
    $mail = $notification->toMail($teacher);
    $rendered = $mail->render();

    expect($rendered)
        ->toContain('Physics')
        ->toContain('75')
        ->toContain('2');
});

// ─── SendWeeklyReports command ────────────────────────────────────────────────

test('SendWeeklyReports command dispatches notifications for students and teachers', function () {
    Notification::fake();

    $teacher = User::factory()->teacher()->create();
    $student = User::factory()->student()->create();
    $class = SchoolClass::factory()->create(['teacher_id' => $teacher->id, 'status' => ClassStatus::Active]);
    $class->students()->attach($student->id, ['enrolled_at' => now()]);
    $session = ClassSession::factory()->active()->create(['class_id' => $class->id]);

    AttendanceRecord::factory()->create([
        'class_session_id' => $session->id,
        'student_id'       => $student->id,
        'status'           => AttendanceStatus::Present,
    ]);

    $this->artisan(SendWeeklyReports::class)->assertSuccessful();

    Notification::assertSentTo($student, WeeklyAttendanceSummaryNotification::class);
    Notification::assertSentTo($teacher, WeeklyAttendanceSummaryNotification::class);
});

test('SendWeeklyReports command handles users with zero sessions gracefully', function () {
    Notification::fake();

    $student = User::factory()->student()->create();
    User::factory()->teacher()->create();

    $this->artisan(SendWeeklyReports::class)->assertSuccessful();

    Notification::assertSentTo($student, WeeklyAttendanceSummaryNotification::class);
});

// ─── Scheduler registration ───────────────────────────────────────────────────

test('SendWeeklyReports is scheduled to run weekly on Sunday at 18:00', function () {
    $schedule = app(\Illuminate\Console\Scheduling\Schedule::class);
    $events = collect($schedule->events());

    $match = $events->first(
        fn ($e) => str_contains($e->command ?? '', 'send-weekly-reports') ||
                   str_contains($e->getSummaryForDisplay(), 'send-weekly-reports')
    );

    expect($match)->not->toBeNull()
        ->and($match->expression)->toBe('0 18 * * 0');
});
