<?php

use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    config()->set('site.settings_file', storage_path('framework/testing/site-settings-admin.json'));
    @unlink(config('site.settings_file'));
});

test('student can view their enrolled classes', function () {
    $student = User::factory()->student()->create();
    $class = SchoolClass::factory()->create();
    $class->students()->attach($student->id, ['enrolled_at' => now()]);

    $this->actingAs($student)
        ->get(route('student.classes.index'))
        ->assertOk()
        ->assertSee($class->name);
});

test('student sees empty state when not enrolled in any class', function () {
    $student = User::factory()->student()->create();

    $this->actingAs($student)
        ->get(route('student.classes.index'))
        ->assertOk()
        ->assertSee('not enrolled');
});

test('teachers cannot access student class routes', function () {
    $teacher = User::factory()->teacher()->create();

    $this->actingAs($teacher)
        ->get(route('student.classes.index'))
        ->assertForbidden();
});
