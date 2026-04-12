<?php

use App\Enums\SessionModality;
use App\Enums\SessionStatus;
use App\Models\ActivityLog;
use App\Models\AttendanceRecord;
use App\Models\ClassSession;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

beforeEach(function () {
    config()->set('site.settings_file', storage_path('framework/testing/site-settings-new-features.json'));
    @unlink(config('site.settings_file'));
});

// ── Admin: Activity Log ──

test('admin can view activity log page', function () {
    $admin = User::factory()->admin()->create();

    ActivityLog::log('test_action', 'Test description');

    $this->actingAs($admin)
        ->get(route('admin.activity-log.index'))
        ->assertSuccessful()
        ->assertSee('Activity Log');
});

test('admin can filter activity log by action', function () {
    $admin = User::factory()->admin()->create();

    ActivityLog::create(['action' => 'created_class', 'description' => 'Created class Math', 'user_id' => $admin->id]);
    ActivityLog::create(['action' => 'blocked_user', 'description' => 'Blocked user John', 'user_id' => $admin->id]);

    $this->actingAs($admin)
        ->get(route('admin.activity-log.index', ['action' => 'created_class']))
        ->assertSuccessful()
        ->assertSee('Created class Math')
        ->assertDontSee('Blocked user John');
});

test('non-admin cannot access activity log', function () {
    $teacher = User::factory()->teacher()->create();

    $this->actingAs($teacher)
        ->get(route('admin.activity-log.index'))
        ->assertForbidden();
});

// ── Admin: Reports ──

test('admin can view reports page', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('admin.reports.index'))
        ->assertSuccessful();
});

test('admin can export attendance CSV', function () {
    $admin = User::factory()->admin()->create();
    $teacher = User::factory()->teacher()->create();
    $class = SchoolClass::factory()->create(['teacher_id' => $teacher->id]);
    $session = ClassSession::factory()->completed()->create(['class_id' => $class->id]);

    $student = User::factory()->create();
    $class->students()->attach($student->id, ['enrolled_at' => now()]);
    AttendanceRecord::factory()->create([
        'class_session_id' => $session->id,
        'student_id' => $student->id,
    ]);

    $this->actingAs($admin)
        ->get(route('admin.reports.export.csv', [
            'start_date' => now()->subMonth()->format('Y-m-d'),
            'end_date' => now()->addDay()->format('Y-m-d'),
        ]))
        ->assertSuccessful()
        ->assertHeader('content-type', 'text/csv; charset=UTF-8');
});

test('admin can export attendance PDF', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('admin.reports.export.pdf', [
            'start_date' => now()->subMonth()->format('Y-m-d'),
            'end_date' => now()->addDay()->format('Y-m-d'),
        ]))
        ->assertSuccessful()
        ->assertHeader('content-type', 'application/pdf');
});

test('admin can view class overview page', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('admin.leaderboard.index'))
        ->assertSuccessful();
});

// ── Teacher: Recurring Sessions ──

test('teacher can create recurring weekly sessions', function () {
    $teacher = User::factory()->teacher()->create();
    $class = SchoolClass::factory()->create(['teacher_id' => $teacher->id]);

    $startTime = now()->addDay()->setTime(10, 0)->format('Y-m-d H:i:s');
    $endTime = now()->addDay()->setTime(12, 0)->format('Y-m-d H:i:s');
    $endDate = now()->addDay()->addWeeks(4)->format('Y-m-d');

    $this->actingAs($teacher)
        ->post(route('teacher.sessions.store', $class), [
            'modality' => SessionModality::Onsite->value,
            'location' => 'Room 101',
            'start_time' => $startTime,
            'end_time' => $endTime,
            'grace_period_minutes' => 15,
            'recurrence_pattern' => 'weekly',
            'recurrence_end_date' => $endDate,
        ])
        ->assertRedirect();

    $sessions = ClassSession::where('class_id', $class->id)->get();

    expect($sessions)->toHaveCount(5) // initial + 4 weeks
        ->and($sessions->first()->recurrence_pattern)->toBe('weekly')
        ->and($sessions->first()->recurrence_group_id)->not->toBeNull();
});

test('teacher can cancel all upcoming recurring sessions', function () {
    $teacher = User::factory()->teacher()->create();
    $class = SchoolClass::factory()->create(['teacher_id' => $teacher->id]);

    $groupId = (string) Str::uuid();
    $sessions = [];
    for ($i = 0; $i < 4; $i++) {
        $sessions[] = ClassSession::factory()->create([
            'class_id' => $class->id,
            'recurrence_group_id' => $groupId,
            'recurrence_pattern' => 'weekly',
            'start_time' => now()->addWeeks($i + 1)->setTime(10, 0),
            'end_time' => now()->addWeeks($i + 1)->setTime(12, 0),
        ]);
    }

    $this->actingAs($teacher)
        ->post(route('teacher.sessions.cancel-upcoming', $sessions[0]))
        ->assertRedirect();

    foreach ($sessions as $session) {
        expect($session->fresh()->status)->toBe(SessionStatus::Cancelled);
    }
});

test('teacher can cancel session with reason', function () {
    $teacher = User::factory()->teacher()->create();
    $class = SchoolClass::factory()->create(['teacher_id' => $teacher->id]);
    $session = ClassSession::factory()->create(['class_id' => $class->id]);

    $this->actingAs($teacher)
        ->post(route('teacher.sessions.cancel', $session), [
            'cancellation_reason' => 'Holiday break',
        ])
        ->assertRedirect();

    expect($session->fresh())
        ->status->toBe(SessionStatus::Cancelled)
        ->cancellation_reason->toBe('Holiday break');
});

// ── Teacher: Student Performance ──

test('teacher can view student performance', function () {
    $teacher = User::factory()->teacher()->create();
    $class = SchoolClass::factory()->create(['teacher_id' => $teacher->id]);
    $student = User::factory()->create();
    $class->students()->attach($student->id, ['enrolled_at' => now()]);

    $this->actingAs($teacher)
        ->get(route('teacher.students.show', [$class, $student]))
        ->assertSuccessful()
        ->assertSee($student->name);
});

// ── Teacher: Class Analytics PDF ──

test('teacher can export class analytics PDF', function () {
    $teacher = User::factory()->teacher()->create();
    $class = SchoolClass::factory()->create(['teacher_id' => $teacher->id]);
    $student = User::factory()->create();
    $class->students()->attach($student->id, ['enrolled_at' => now()]);

    $this->actingAs($teacher)
        ->get(route('teacher.classes.analytics.pdf', $class))
        ->assertSuccessful()
        ->assertHeader('content-type', 'application/pdf');
});

// ── Student: Notification Preferences ──

test('student can view notification preferences', function () {
    $student = User::factory()->create();

    $this->actingAs($student)
        ->get(route('student.notifications.edit'))
        ->assertSuccessful();
});

test('student can update notification preferences', function () {
    $student = User::factory()->create();

    $this->actingAs($student)
        ->patch(route('student.notifications.update'), [
            'session_started' => true,
            'weekly_summary' => false,
            'absence_alert' => true,
            'excuse_updates' => false,
        ])
        ->assertRedirect();

    $student->refresh();
    expect($student->notification_preferences)->toBe([
        'session_started' => true,
        'weekly_summary' => false,
        'absence_alert' => true,
        'excuse_updates' => false,
    ]);
});

// ── Student: Attendance Calendar ──

test('student can view attendance calendar', function () {
    $student = User::factory()->create();

    $this->actingAs($student)
        ->get(route('student.calendar.index'))
        ->assertSuccessful();
});

// ── Dashboard: Upcoming Sessions ──

test('teacher dashboard shows upcoming sessions', function () {
    $teacher = User::factory()->teacher()->create();
    $class = SchoolClass::factory()->create(['teacher_id' => $teacher->id]);
    ClassSession::factory()->create([
        'class_id' => $class->id,
        'start_time' => now()->addDay()->setTime(10, 0),
        'end_time' => now()->addDay()->setTime(12, 0),
    ]);

    $this->actingAs($teacher)
        ->get(route('dashboard'))
        ->assertSuccessful()
        ->assertSee('Upcoming Sessions');
});

test('student dashboard shows upcoming sessions', function () {
    $student = User::factory()->create();
    $teacher = User::factory()->teacher()->create();
    $class = SchoolClass::factory()->create(['teacher_id' => $teacher->id]);
    $class->students()->attach($student->id, ['enrolled_at' => now()]);

    ClassSession::factory()->create([
        'class_id' => $class->id,
        'start_time' => now()->addDay()->setTime(10, 0),
        'end_time' => now()->addDay()->setTime(12, 0),
    ]);

    $this->actingAs($student)
        ->get(route('dashboard'))
        ->assertSuccessful()
        ->assertSee('Upcoming Sessions');
});

// ── Activity Log: Audit Trail ──

test('activity log records admin user actions', function () {
    $admin = User::factory()->admin()->create();
    $user = User::factory()->create();

    $this->actingAs($admin)
        ->patch(route('admin.users.update', $user), [
            'name' => 'Updated Name',
            'email' => $user->email,
            'role' => $user->role->value,
        ]);

    expect(ActivityLog::where('action', 'updated_user')->exists())->toBeTrue();
});

test('activity log records teacher class actions', function () {
    $teacher = User::factory()->teacher()->create();

    $this->actingAs($teacher)
        ->post(route('teacher.classes.store'), [
            'name' => 'Test Class',
        ]);

    expect(ActivityLog::where('action', 'created_class')->exists())->toBeTrue();
});
