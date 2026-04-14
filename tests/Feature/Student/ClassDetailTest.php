<?php

use App\Enums\AttendanceMarkedBy;
use App\Enums\AttendanceStatus;
use App\Enums\ClassStatus;
use App\Enums\SessionStatus;
use App\Models\AttendanceRecord;
use App\Models\ClassSession;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    config(['site.timezone' => 'Asia/Manila']);
    $this->student = User::factory()->student()->create();
    $this->teacher = User::factory()->teacher()->create();
    $this->class = SchoolClass::factory()->create(['teacher_id' => $this->teacher->id]);
    $this->class->students()->attach($this->student->id, ['enrolled_at' => now()]);
});

test('student can view class detail page', function () {
    $session = ClassSession::factory()->completed()->create(['class_id' => $this->class->id]);
    AttendanceRecord::factory()->create([
        'class_session_id' => $session->id,
        'student_id' => $this->student->id,
        'status' => AttendanceStatus::Present,
    ]);

    $response = $this->actingAs($this->student)->get(route('student.classes.show', $this->class));

    $response->assertSuccessful();
    $response->assertSee($this->class->name);
    $response->assertSee($this->teacher->name);
    $response->assertSee('Present');
});

test('student class detail page shows attendance statistics', function () {
    $sessions = ClassSession::factory()->completed()->count(3)->create(['class_id' => $this->class->id]);

    AttendanceRecord::factory()->create([
        'class_session_id' => $sessions[0]->id,
        'student_id' => $this->student->id,
        'status' => AttendanceStatus::Present,
    ]);
    AttendanceRecord::factory()->create([
        'class_session_id' => $sessions[1]->id,
        'student_id' => $this->student->id,
        'status' => AttendanceStatus::Late,
    ]);
    AttendanceRecord::factory()->create([
        'class_session_id' => $sessions[2]->id,
        'student_id' => $this->student->id,
        'status' => AttendanceStatus::Absent,
        'scanned_at' => null,
    ]);

    $response = $this->actingAs($this->student)->get(route('student.classes.show', $this->class));

    $response->assertSuccessful();
    $response->assertSeeInOrder(['Present', '1']);
    $response->assertSeeInOrder(['Late', '1']);
    $response->assertSeeInOrder(['Absent', '1']);
});

test('student cannot view class they are not enrolled in', function () {
    $otherClass = SchoolClass::factory()->create();

    $response = $this->actingAs($this->student)->get(route('student.classes.show', $otherClass));

    $response->assertForbidden();
});

test('student class detail page shows charts', function () {
    $session = ClassSession::factory()->completed()->create(['class_id' => $this->class->id]);
    AttendanceRecord::factory()->create([
        'class_session_id' => $session->id,
        'student_id' => $this->student->id,
    ]);

    $response = $this->actingAs($this->student)->get(route('student.classes.show', $this->class));

    $response->assertSuccessful();
    $response->assertSee('data-chart="line"', false);
    $response->assertSee('data-chart="pie"', false);
});

test('student classes index links to class detail', function () {
    $response = $this->actingAs($this->student)->get(route('student.classes.index'));

    $response->assertSuccessful();
    $response->assertSee(route('student.classes.show', $this->class));
});
