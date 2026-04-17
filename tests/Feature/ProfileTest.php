<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    config()->set('site.settings_file', storage_path('framework/testing/site-settings-admin.json'));
    @unlink(config('site.settings_file'));
});

test('user can view their own profile', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('profile.show', $user))
        ->assertOk()
        ->assertSee($user->name);
});

test('user can view another user\'s profile', function () {
    $user = User::factory()->create();
    $other = User::factory()->teacher()->create();

    $this->actingAs($user)
        ->get(route('profile.show', $other))
        ->assertOk()
        ->assertSee($other->name)
        ->assertSee($other->role->value);
});

test('profile shows initials when no avatar is set', function () {
    $user = User::factory()->create(['name' => 'Jane Doe', 'avatar_path' => null]);
    $initial = mb_strtoupper(mb_substr('Jane Doe', 0, 1));

    $this->actingAs($user)
        ->get(route('profile.show', $user))
        ->assertOk()
        ->assertSee($initial);
});

test('user can view the edit profile form', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('profile.edit'))
        ->assertOk();
});

test('user can upload a valid avatar', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $file = UploadedFile::fake()->image('avatar.jpg', 100, 100);

    $this->actingAs($user)
        ->patch(route('profile.update'), ['avatar' => $file])
        ->assertRedirect();

    $user->refresh();
    expect($user->avatar_path)->not->toBeNull();
    Storage::disk('public')->assertExists($user->avatar_path);
});

test('user can upload a valid banner', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $file = UploadedFile::fake()->image('banner.png', 1200, 400);

    $this->actingAs($user)
        ->patch(route('profile.update'), ['banner' => $file])
        ->assertRedirect();

    $user->refresh();
    expect($user->banner_path)->not->toBeNull();
    Storage::disk('public')->assertExists($user->banner_path);
});

test('old avatar is deleted when a new one is uploaded', function () {
    Storage::fake('public');

    $user = User::factory()->create();

    // First upload (jpg)
    $first = UploadedFile::fake()->image('first.jpg');
    $this->actingAs($user)->patch(route('profile.update'), ['avatar' => $first]);
    $user->refresh();
    $firstPath = $user->avatar_path;

    // Second upload (png — different extension)
    $second = UploadedFile::fake()->image('second.png');
    $this->actingAs($user)->patch(route('profile.update'), ['avatar' => $second]);
    $user->refresh();

    Storage::disk('public')->assertMissing($firstPath);
    Storage::disk('public')->assertExists($user->avatar_path);
    expect($user->avatar_path)->not->toBe($firstPath);
});

test('avatar can be replaced with same extension', function () {
    Storage::fake('public');

    $user = User::factory()->create();

    // First upload (png)
    $first = UploadedFile::fake()->image('first.png');
    $this->actingAs($user)->patch(route('profile.update'), ['avatar' => $first]);
    $user->refresh();
    $firstPath = $user->avatar_path;

    // Advance time so the timestamp differs
    $this->travel(1)->seconds();

    // Second upload (png — same extension)
    $second = UploadedFile::fake()->image('second.png');
    $this->actingAs($user)
        ->patch(route('profile.update'), ['avatar' => $second])
        ->assertRedirect();
    $user->refresh();

    expect($user->avatar_path)->not->toBeNull()
        ->and($user->avatar_path)->not->toBe($firstPath);
    Storage::disk('public')->assertMissing($firstPath);
    Storage::disk('public')->assertExists($user->avatar_path);
});

test('banner can be replaced with same extension', function () {
    Storage::fake('public');

    $user = User::factory()->create();

    // First upload
    $first = UploadedFile::fake()->image('first.jpg', 1200, 400);
    $this->actingAs($user)->patch(route('profile.update'), ['banner' => $first]);
    $user->refresh();
    $firstPath = $user->banner_path;

    $this->travel(1)->seconds();

    // Second upload — same extension
    $second = UploadedFile::fake()->image('second.jpg', 1200, 400);
    $this->actingAs($user)
        ->patch(route('profile.update'), ['banner' => $second])
        ->assertRedirect();
    $user->refresh();

    expect($user->banner_path)->not->toBeNull()
        ->and($user->banner_path)->not->toBe($firstPath);
    Storage::disk('public')->assertMissing($firstPath);
    Storage::disk('public')->assertExists($user->banner_path);
});

test('validation rejects avatar that is too large', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $file = UploadedFile::fake()->image('big.jpg')->size(3000);

    $this->actingAs($user)
        ->patch(route('profile.update'), ['avatar' => $file])
        ->assertInvalid(['avatar']);
});

test('validation rejects non-image file as avatar', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

    $this->actingAs($user)
        ->patch(route('profile.update'), ['avatar' => $file])
        ->assertInvalid(['avatar']);
});

test('unauthenticated user cannot access profile routes', function () {
    $user = User::factory()->create();

    $this->get(route('profile.show', $user))->assertRedirect(route('login'));
    $this->get(route('profile.edit'))->assertRedirect(route('login'));
    $this->patch(route('profile.update'))->assertRedirect(route('login'));
});
