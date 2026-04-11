<?php

use App\Enums\ClassStatus;
use App\Enums\UserRole;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    config()->set('site.settings_file', storage_path('framework/testing/site-settings-admin.json'));
    @unlink(config('site.settings_file'));
});

test('teacher can view their classes list', function () {
    $teacher = User::factory()->teacher()->create();
    $class = SchoolClass::factory()->create(['teacher_id' => $teacher->id]);

    $this->actingAs($teacher)
        ->get(route('teacher.classes.index'))
        ->assertOk()
        ->assertSee($class->name);
});

test('teacher can view create class form', function () {
    $teacher = User::factory()->teacher()->create();

    $this->actingAs($teacher)
        ->get(route('teacher.classes.create'))
        ->assertOk();
});

test('teacher can create a class', function () {
    $teacher = User::factory()->teacher()->create();

    $this->actingAs($teacher)
        ->post(route('teacher.classes.store'), [
            'name' => 'ICT 101',
            'section' => 'Section A',
            'description' => 'Introduction to computing.',
        ])
        ->assertRedirect();

    $class = SchoolClass::where('name', 'ICT 101')->first();

    expect($class)->not->toBeNull()
        ->and($class->teacher_id)->toBe($teacher->id)
        ->and($class->status)->toBe(ClassStatus::Active);
});

test('teacher can view their class', function () {
    $teacher = User::factory()->teacher()->create();
    $class = SchoolClass::factory()->create(['teacher_id' => $teacher->id]);

    $this->actingAs($teacher)
        ->get(route('teacher.classes.show', $class))
        ->assertOk()
        ->assertSee($class->name);
});

test('teacher cannot view another teacher\'s class', function () {
    $teacher = User::factory()->teacher()->create();
    $other = User::factory()->teacher()->create();
    $class = SchoolClass::factory()->create(['teacher_id' => $other->id]);

    $this->actingAs($teacher)
        ->get(route('teacher.classes.show', $class))
        ->assertForbidden();
});

test('teacher can update their class', function () {
    $teacher = User::factory()->teacher()->create();
    $class = SchoolClass::factory()->create(['teacher_id' => $teacher->id]);

    $this->actingAs($teacher)
        ->patch(route('teacher.classes.update', $class), [
            'name' => 'Updated Name',
            'section' => null,
            'description' => null,
        ])
        ->assertRedirect(route('teacher.classes.show', $class));

    expect($class->fresh()->name)->toBe('Updated Name');
});

test('teacher can archive a class', function () {
    $teacher = User::factory()->teacher()->create();
    $class = SchoolClass::factory()->create(['teacher_id' => $teacher->id]);

    $this->actingAs($teacher)
        ->post(route('teacher.classes.archive', $class))
        ->assertRedirect(route('teacher.classes.index'));

    expect($class->fresh()->status)->toBe(ClassStatus::Archived);
});

test('teacher can enroll a student', function () {
    $teacher = User::factory()->teacher()->create();
    $student = User::factory()->student()->create();
    $class = SchoolClass::factory()->create(['teacher_id' => $teacher->id]);

    $this->actingAs($teacher)
        ->post(route('teacher.classes.enroll', $class), [
            'students' => [$student->id],
        ])
        ->assertRedirect(route('teacher.classes.show', $class));

    expect($class->students()->where('student_id', $student->id)->exists())->toBeTrue();
});

test('teacher can bulk enroll students', function () {
    $teacher = User::factory()->teacher()->create();
    $students = User::factory()->student()->count(3)->create();
    $class = SchoolClass::factory()->create(['teacher_id' => $teacher->id]);

    $this->actingAs($teacher)
        ->post(route('teacher.classes.enroll', $class), [
            'students' => $students->pluck('id')->all(),
        ])
        ->assertRedirect(route('teacher.classes.show', $class));

    expect($class->students()->count())->toBe(3);
});

test('teacher cannot enroll same student twice', function () {
    $teacher = User::factory()->teacher()->create();
    $student = User::factory()->student()->create();
    $class = SchoolClass::factory()->create(['teacher_id' => $teacher->id]);
    $class->students()->attach($student->id, ['enrolled_at' => now()]);

    $this->actingAs($teacher)
        ->post(route('teacher.classes.enroll', $class), [
            'students' => [$student->id],
        ])
        ->assertRedirect(route('teacher.classes.show', $class));

    expect($class->students()->count())->toBe(1);
});

test('teacher can remove a student from class', function () {
    $teacher = User::factory()->teacher()->create();
    $student = User::factory()->student()->create();
    $class = SchoolClass::factory()->create(['teacher_id' => $teacher->id]);
    $class->students()->attach($student->id, ['enrolled_at' => now()]);

    $this->actingAs($teacher)
        ->delete(route('teacher.classes.unenroll', [$class, $student]))
        ->assertRedirect(route('teacher.classes.show', $class));

    expect($class->students()->count())->toBe(0);
});

test('teacher can search students for enrollment', function () {
    $teacher = User::factory()->teacher()->create();
    $student = User::factory()->student()->create(['name' => 'Alice Wonderland', 'email' => 'alice@example.com']);
    $class = SchoolClass::factory()->create(['teacher_id' => $teacher->id]);

    $this->actingAs($teacher)
        ->getJson(route('teacher.classes.students.search', $class) . '?q=alice')
        ->assertOk()
        ->assertJsonCount(1)
        ->assertJsonFragment(['name' => 'Alice Wonderland', 'email' => 'alice@example.com']);
});

test('student search excludes already enrolled students', function () {
    $teacher = User::factory()->teacher()->create();
    $enrolled = User::factory()->student()->create(['name' => 'Bob Smith']);
    $notEnrolled = User::factory()->student()->create(['name' => 'Bobby Jones']);
    $class = SchoolClass::factory()->create(['teacher_id' => $teacher->id]);
    $class->students()->attach($enrolled->id, ['enrolled_at' => now()]);

    $response = $this->actingAs($teacher)
        ->getJson(route('teacher.classes.students.search', $class) . '?q=bob')
        ->assertOk();

    $ids = collect($response->json())->pluck('id');
    expect($ids)->toContain($notEnrolled->id)
        ->and($ids)->not->toContain($enrolled->id);
});

test('student search excludes non-student users', function () {
    $teacher = User::factory()->teacher()->create(['name' => 'Teacher Test']);
    $admin = User::factory()->admin()->create(['name' => 'Admin Test']);
    $student = User::factory()->student()->create(['name' => 'Student Test']);
    $class = SchoolClass::factory()->create(['teacher_id' => $teacher->id]);

    $response = $this->actingAs($teacher)
        ->getJson(route('teacher.classes.students.search', $class) . '?q=test')
        ->assertOk();

    $ids = collect($response->json())->pluck('id');
    expect($ids)->toContain($student->id)
        ->and($ids)->not->toContain($teacher->id)
        ->and($ids)->not->toContain($admin->id);
});

test('students cannot access teacher class routes', function () {
    $student = User::factory()->student()->create();

    $this->actingAs($student)
        ->get(route('teacher.classes.index'))
        ->assertForbidden();
});

test('class creation requires a name', function () {
    $teacher = User::factory()->teacher()->create();

    $this->actingAs($teacher)
        ->post(route('teacher.classes.store'), ['name' => ''])
        ->assertInvalid(['name']);
});
