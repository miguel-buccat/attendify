<?php

use App\Enums\UserRole;
use App\Models\User;
use App\Support\SiteSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    config()->set('site.settings_file', storage_path('framework/testing/site-settings-feature.json'));

    @unlink(config('site.settings_file'));
});

test('landing redirects to new index when no admin exists', function () {
    $this->get(route('landing'))
        ->assertRedirect(route('new.index'));
});

test('new index redirects to landing once setup is complete', function () {
    User::query()->create([
        'name' => 'System Admin',
        'email' => 'admin@example.com',
        'password' => 'password',
        'email_verified_at' => now(),
        'role' => UserRole::Admin->value,
    ]);

    $settings = app(SiteSettings::class);
    $settings->set('institution_logo', 'site-settings/logo.png');
    $settings->set('landing_banner', 'site-settings/banner.png');

    $this->get(route('new.index'))
        ->assertRedirect(route('landing'));
});

test('setup step 1 creates an email verified admin account', function () {
    $this->post(route('new.setup.admin'), [
        'name' => 'Setup Admin',
        'email' => 'setup-admin@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])
        ->assertRedirect(route('new.setup'));

    $this->assertDatabaseHas('users', [
        'email' => 'setup-admin@example.com',
        'role' => UserRole::Admin->value,
    ]);

    $admin = User::query()->where('email', 'setup-admin@example.com')->firstOrFail();

    expect($admin->email_verified_at)->not->toBeNull();
});

test('setup step 2 stores site settings and redirects to landing', function () {
    Storage::fake('public');

    User::query()->create([
        'name' => 'System Admin',
        'email' => 'admin@example.com',
        'password' => 'password',
        'email_verified_at' => now(),
        'role' => UserRole::Admin->value,
    ]);

    $response = $this->post(route('new.setup.settings'), [
        'institution_name' => 'Erovoutika Robotics and Automation Solutions',
        'institution_logo' => UploadedFile::fake()->image('logo.png'),
        'landing_banner' => UploadedFile::fake()->image('banner.png'),
    ]);

    $response->assertRedirect(route('landing'));

    $settings = app(SiteSettings::class);

    $logoPath = $settings->get('institution_logo');
    $bannerPath = $settings->get('landing_banner');

    expect($settings->get('institution_name'))->toBe('Erovoutika Robotics and Automation Solutions');
    expect($logoPath)->toStartWith(rtrim(config('app.url'), '/').'/storage/site-settings/');
    expect($bannerPath)->toStartWith(rtrim(config('app.url'), '/').'/storage/site-settings/');

    $logoRelativePath = ltrim(str_replace(rtrim(config('app.url'), '/').'/storage/', '', $logoPath), '/');
    $bannerRelativePath = ltrim(str_replace(rtrim(config('app.url'), '/').'/storage/', '', $bannerPath), '/');

    Storage::disk('public')->assertExists($logoRelativePath);
    Storage::disk('public')->assertExists($bannerRelativePath);
});

test('admin account password is hashed and not stored in plain text', function () {
    $this->post(route('new.setup.admin'), [
        'name' => 'Setup Admin',
        'email' => 'setup-admin@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $admin = User::query()->where('email', 'setup-admin@example.com')->firstOrFail();

    expect($admin->password)->not->toBe('password')
        ->and(Hash::check('password', $admin->password))->toBeTrue();
});

test('setup step 2 rejects non-image files', function () {
    Storage::fake('public');

    User::query()->create([
        'name' => 'System Admin',
        'email' => 'admin@example.com',
        'password' => 'password',
        'email_verified_at' => now(),
        'role' => UserRole::Admin->value,
    ]);

    $this->post(route('new.setup.settings'), [
        'institution_name' => 'Test School',
        'institution_logo' => UploadedFile::fake()->create('malicious.pdf', 100, 'application/pdf'),
        'landing_banner' => UploadedFile::fake()->image('banner.png'),
    ])->assertInvalid(['institution_logo']);
});

test('setup step 2 rejects svg files to prevent xss', function () {
    Storage::fake('public');

    User::query()->create([
        'name' => 'System Admin',
        'email' => 'admin@example.com',
        'password' => 'password',
        'email_verified_at' => now(),
        'role' => UserRole::Admin->value,
    ]);

    $this->post(route('new.setup.settings'), [
        'institution_name' => 'Test School',
        'institution_logo' => UploadedFile::fake()->create('logo.svg', 10, 'image/svg+xml'),
        'landing_banner' => UploadedFile::fake()->image('banner.png'),
    ])->assertInvalid(['institution_logo']);
});
