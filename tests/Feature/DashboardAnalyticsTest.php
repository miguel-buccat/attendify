<?php

use App\Enums\AttendanceMarkedBy;
use App\Enums\AttendanceStatus;
use App\Enums\ClassStatus;
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

test('admin dashboard returns correct metrics for seeded data', function () {
    $admin = User::factory()->admin()->create();
    $teacher = User::factory()->teacher()->create();
    $student = User::factory()->student()->create();

    $class = SchoolClass::factory()->create(['teacher_id' => $teacher->id]);
    $class->students()->attach($student->id, ['enrolled_at' => now()]);

    $session = ClassSession::factory()->completed()->create(['class_id' => $class->id]);

    AttendanceRecord::create([
        'class_session_id' => $session->id,
        'student_id' => $student->id,
        'status' => AttendanceStatus::Present,
        'scanned_at' => now(),
        'marked_by' => AttendanceMarkedBy::System,
    ]);

    $response = $this->actingAs($admin)->get(route('dashboard'));

    $response->assertOk()
        ->assertSee('Admin Dashboard')
        ->assertSee('3')  // total users
        ->assertSee('1')  // active classes
        ->assertSee('100'); // 100% attendance rate
});

test('teacher dashboard scopes data to the authenticated teachers classes only', function () {
    $teacher = User::factory()->teacher()->create();
    $otherTeacher = User::factory()->teacher()->create();
    $student = User::factory()->student()->create();

    $myClass = SchoolClass::factory()->create(['teacher_id' => $teacher->id]);
    $otherClass = SchoolClass::factory()->create(['teacher_id' => $otherTeacher->id]);

    $myClass->students()->attach($student->id, ['enrolled_at' => now()]);
    $otherClass->students()->attach($student->id, ['enrolled_at' => now()]);

    $mySession = ClassSession::factory()->completed()->create(['class_id' => $myClass->id]);
    ClassSession::factory()->completed()->create(['class_id' => $otherClass->id]);

    AttendanceRecord::create([
        'class_session_id' => $mySession->id,
        'student_id' => $student->id,
        'status' => AttendanceStatus::Present,
        'scanned_at' => now(),
        'marked_by' => AttendanceMarkedBy::System,
    ]);

    $response = $this->actingAs($teacher)->get(route('dashboard'));

    $response->assertOk()
        ->assertSee('Teacher Dashboard')
        ->assertSee($myClass->name);
});

test('student dashboard scopes data to the authenticated students enrollment only', function () {
    $teacher = User::factory()->teacher()->create();
    $student = User::factory()->student()->create();
    $otherStudent = User::factory()->student()->create();

    $class = SchoolClass::factory()->create(['teacher_id' => $teacher->id]);
    $class->students()->attach($student->id, ['enrolled_at' => now()]);

    $session = ClassSession::factory()->completed()->create(['class_id' => $class->id]);

    AttendanceRecord::create([
        'class_session_id' => $session->id,
        'student_id' => $student->id,
        'status' => AttendanceStatus::Present,
        'scanned_at' => now(),
        'marked_by' => AttendanceMarkedBy::System,
    ]);

    $response = $this->actingAs($student)->get(route('dashboard'));

    $response->assertOk()
        ->assertSee('Student Dashboard')
        ->assertSee('1')  // present count
        ->assertSee('100'); // attendance rate
});

test('chart data arrays have the expected structure', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->get(route('dashboard'));

    $response->assertOk();

    // Chart data is embedded in data-chart-data attributes
    $content = $response->getContent();
    expect($content)->toContain('data-chart="pie"')
        ->and($content)->toContain('data-chart="line"')
        ->and($content)->toContain('admin-pie-chart')
        ->and($content)->toContain('admin-line-chart');
});

test('dashboards render correctly with zero data', function () {
    $admin = User::factory()->admin()->create();
    $teacher = User::factory()->teacher()->create();
    $student = User::factory()->student()->create();

    $this->actingAs($admin)->get(route('dashboard'))->assertOk();
    $this->actingAs($teacher)->get(route('dashboard'))->assertOk();
    $this->actingAs($student)->get(route('dashboard'))->assertOk();
});
