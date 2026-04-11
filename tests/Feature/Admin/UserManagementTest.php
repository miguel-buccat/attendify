<?php

use App\Enums\UserRole;
use App\Jobs\ExpireStaleInvitations;
use App\Models\Invitation;
use App\Models\User;
use App\Notifications\Auth\InvitationNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

beforeEach(function () {
    config()->set('site.settings_file', storage_path('framework/testing/site-settings-admin.json'));

    @unlink(config('site.settings_file'));
});

test('admin can view the users list', function () {
    $admin = User::factory()->admin()->create();
    $teacher = User::factory()->teacher()->create();

    $this->actingAs($admin)
        ->get(route('admin.users.index'))
        ->assertOk()
        ->assertSee($teacher->name)
        ->assertSee($teacher->email);
});

test('admin can view pending invitations on users list', function () {
    $admin = User::factory()->admin()->create();
    $invitation = Invitation::factory()->create(['invited_by' => $admin->id]);

    $this->actingAs($admin)
        ->get(route('admin.users.index'))
        ->assertOk()
        ->assertSee($invitation->email);
});

test('admin can view the invite user form', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('admin.users.invite'))
        ->assertOk();
});

test('admin can send an invitation', function () {
    Notification::fake();

    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->post(route('admin.users.invite.send'), [
            'invitees' => [
                ['email' => 'newteacher@example.com', 'role' => UserRole::Teacher->value],
            ],
        ])
        ->assertRedirect(route('admin.users.index'));

    $invitation = Invitation::where('email', 'newteacher@example.com')->first();

    expect($invitation)->not->toBeNull()
        ->and($invitation->role)->toBe(UserRole::Teacher);

    Notification::assertSentOnDemand(
        InvitationNotification::class,
        fn ($notification, $channels, $notifiable) => $notifiable->routes['mail'] === 'newteacher@example.com'
    );
});

test('invitation email is dispatched (queued)', function () {
    Notification::fake();

    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->post(route('admin.users.invite.send'), [
        'invitees' => [
            ['email' => 'queued@example.com', 'role' => UserRole::Student->value],
        ],
    ]);

    Notification::assertSentOnDemand(InvitationNotification::class);
});

test('invitation fails validation for duplicate user email', function () {
    $admin = User::factory()->admin()->create();
    $existing = User::factory()->create(['email' => 'existing@example.com']);

    $this->actingAs($admin)
        ->post(route('admin.users.invite.send'), [
            'invitees' => [
                ['email' => $existing->email, 'role' => UserRole::Student->value],
            ],
        ])
        ->assertInvalid(['invitees.0.email']);
});

test('invitation fails validation for already invited email', function () {
    $admin = User::factory()->admin()->create();
    Invitation::factory()->create(['email' => 'pending@example.com', 'invited_by' => $admin->id]);

    $this->actingAs($admin)
        ->post(route('admin.users.invite.send'), [
            'invitees' => [
                ['email' => 'pending@example.com', 'role' => UserRole::Student->value],
            ],
        ])
        ->assertInvalid(['invitees.0.email']);
});

test('invitation fails validation for invalid role', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->post(route('admin.users.invite.send'), [
            'invitees' => [
                ['email' => 'someone@example.com', 'role' => 'Admin'],
            ],
        ])
        ->assertInvalid(['invitees.0.role']);
});

test('non-admin cannot access user management routes', function () {
    $teacher = User::factory()->teacher()->create();

    $this->actingAs($teacher)
        ->get(route('admin.users.index'))
        ->assertForbidden();
});

test('non-admin cannot send invitations', function () {
    $student = User::factory()->student()->create();

    $this->actingAs($student)
        ->post(route('admin.users.invite.send'), [
            'invitees' => [
                ['email' => 'someone@example.com', 'role' => UserRole::Teacher->value],
            ],
        ])
        ->assertForbidden();
});

test('valid token shows accept invitation form', function () {
    $invitation = Invitation::factory()->forTeacher()->create();

    $this->get(route('invitation.accept', ['token' => $invitation->token]))
        ->assertOk()
        ->assertSee($invitation->email);
});

test('accepting valid invitation creates user and logs in', function () {
    $invitation = Invitation::factory()->forTeacher()->create();

    $this->post(route('invitation.accept.store', ['token' => $invitation->token]), [
        'name' => 'New Teacher',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ])->assertRedirect(route('dashboard'));

    $this->assertAuthenticated();

    $user = User::where('email', $invitation->email)->first();
    expect($user)->not->toBeNull()
        ->and($user->role)->toBe(UserRole::Teacher)
        ->and($user->email_verified_at)->not->toBeNull();

    expect(Invitation::find($invitation->id)->accepted_at)->not->toBeNull();
});

test('expired token shows error view', function () {
    $invitation = Invitation::factory()->expired()->create();

    $this->get(route('invitation.accept', ['token' => $invitation->token]))
        ->assertOk()
        ->assertSee('expired');
});

test('already accepted token shows error view', function () {
    $invitation = Invitation::factory()->accepted()->create();

    $this->get(route('invitation.accept', ['token' => $invitation->token]))
        ->assertOk()
        ->assertSee('already been used');
});

test('invalid token returns 404', function () {
    $this->get(route('invitation.accept', ['token' => 'nonexistenttoken']))
        ->assertNotFound();
});

test('admin can send bulk invitations in a single request', function () {
    Notification::fake();

    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->post(route('admin.users.invite.send'), [
            'invitees' => [
                ['email' => 'teacher@example.com', 'role' => UserRole::Teacher->value],
                ['email' => 'student@example.com', 'role' => UserRole::Student->value],
            ],
        ])
        ->assertRedirect(route('admin.users.index'))
        ->assertSessionHas('success', '2 invitations sent successfully.');

    expect(Invitation::where('email', 'teacher@example.com')->exists())->toBeTrue();
    expect(Invitation::where('email', 'student@example.com')->exists())->toBeTrue();

    Notification::assertSentOnDemandTimes(InvitationNotification::class, 2);
});

test('stale invitations job deletes expired records', function () {
    $expired = Invitation::factory()->expired()->create();
    $valid = Invitation::factory()->create();

    (new ExpireStaleInvitations)->handle();

    expect(Invitation::find($expired->id))->toBeNull();
    expect(Invitation::find($valid->id))->not->toBeNull();
});
