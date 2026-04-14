<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;

uses(RefreshDatabase::class);

beforeEach(function () {
    config()->set('site.settings_file', storage_path('framework/testing/site-settings-role.json'));
    @unlink(config('site.settings_file'));

    Route::middleware(['web', 'auth', 'role:admin'])
        ->get('/test-admin-only', fn () => 'admin ok')
        ->name('test.admin');

    Route::middleware(['web', 'auth', 'role:teacher'])
        ->get('/test-teacher-only', fn () => 'teacher ok')
        ->name('test.teacher');

    Route::middleware(['web', 'auth', 'role:student'])
        ->get('/test-student-only', fn () => 'student ok')
        ->name('test.student');

    Route::middleware(['web', 'auth', 'role:admin,teacher'])
        ->get('/test-admin-or-teacher', fn () => 'multi ok')
        ->name('test.multi');
});

// --- Admin access ---

test('admin can access admin-only route', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get('/test-admin-only')
        ->assertOk()
        ->assertSee('admin ok');
});

test('teacher is forbidden from admin-only route', function () {
    $teacher = User::factory()->teacher()->create();

    $this->actingAs($teacher)
        ->get('/test-admin-only')
        ->assertForbidden();
});

test('student is forbidden from admin-only route', function () {
    $student = User::factory()->create();

    $this->actingAs($student)
        ->get('/test-admin-only')
        ->assertForbidden();
});

// --- Teacher access ---

test('teacher can access teacher-only route', function () {
    $teacher = User::factory()->teacher()->create();

    $this->actingAs($teacher)
        ->get('/test-teacher-only')
        ->assertOk()
        ->assertSee('teacher ok');
});

test('admin is forbidden from teacher-only route', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get('/test-teacher-only')
        ->assertForbidden();
});

test('student is forbidden from teacher-only route', function () {
    $student = User::factory()->create();

    $this->actingAs($student)
        ->get('/test-teacher-only')
        ->assertForbidden();
});

// --- Multi-role access ---

test('admin can access admin-or-teacher route', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get('/test-admin-or-teacher')
        ->assertOk()
        ->assertSee('multi ok');
});

test('teacher can access admin-or-teacher route', function () {
    $teacher = User::factory()->teacher()->create();

    $this->actingAs($teacher)
        ->get('/test-admin-or-teacher')
        ->assertOk()
        ->assertSee('multi ok');
});

test('student is forbidden from admin-or-teacher route', function () {
    $student = User::factory()->create();

    $this->actingAs($student)
        ->get('/test-admin-or-teacher')
        ->assertForbidden();
});
