<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\InviteUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\ActivityLog;
use App\Models\Invitation;
use App\Models\User;
use App\Notifications\Auth\InvitationNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Str;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::orderBy('name');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                    ->orWhere('email', 'ilike', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->input('role'));
        }

        $users = $query->paginate(20)->withQueryString();
        $invitations = Invitation::with('inviter')->pending()->orderByDesc('created_at')->get();

        return view('admin.users.index', compact('users', 'invitations'));
    }

    public function show(User $user): JsonResponse
    {
        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role->value,
            'status' => $user->status->value,
            'avatar_url' => $user->avatarUrl,
            'email_verified_at' => $user->email_verified_at?->format('F j, Y'),
            'created_at' => $user->created_at->format('F j, Y'),
            'updated_at' => $user->updated_at->format('F j, Y, g:i A'),
            'status_reason' => $user->status_reason,
            'guardian_name' => $user->guardian_name,
            'guardian_email' => $user->guardian_email,
            'is_student' => $user->role === \App\Enums\UserRole::Student,
            'is_self' => $user->id === auth()->id(),
            'update_url' => route('admin.users.update', $user),
            'block_url' => route('admin.users.block', $user),
            'unblock_url' => route('admin.users.unblock', $user),
            'archive_url' => route('admin.users.archive', $user),
            'profile_url' => route('profile.show', $user),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $user->update($request->validated());

        ActivityLog::log('updated_user', "Updated user {$user->name}", $user);

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    public function block(User $user): RedirectResponse
    {
        abort_if($user->id === auth()->id(), 403, 'You cannot block yourself.');

        $user->update([
            'status' => UserStatus::Blocked,
            'status_reason' => request()->validate(['reason' => ['nullable', 'string', 'max:500']])['reason'] ?? null,
        ]);

        ActivityLog::log('blocked_user', "Blocked user {$user->name}", $user);

        return redirect()->route('admin.users.index')->with('success', "{$user->name}'s account has been temporarily blocked.");
    }

    public function unblock(User $user): RedirectResponse
    {
        $user->update(['status' => UserStatus::Active, 'status_reason' => null]);

        ActivityLog::log('unblocked_user', "Restored user {$user->name}", $user);

        return redirect()->route('admin.users.index')->with('success', "{$user->name}'s account has been restored.");
    }

    public function archive(User $user): RedirectResponse
    {
        abort_if($user->id === auth()->id(), 403, 'You cannot archive yourself.');

        $user->update([
            'status' => UserStatus::Archived,
            'status_reason' => request()->validate(['reason' => ['nullable', 'string', 'max:500']])['reason'] ?? null,
        ]);

        ActivityLog::log('archived_user', "Archived user {$user->name}", $user);

        return redirect()->route('admin.users.index')->with('success', "{$user->name}'s account has been archived.");
    }

    public function invite(): View
    {
        return view('admin.users.invite');
    }

    public function sendInvitation(InviteUserRequest $request): RedirectResponse
    {
        $count = 0;

        foreach ($request->validated('invitees') as $invitee) {
            $invitation = Invitation::create([
                'email' => $invitee['email'],
                'name' => $invitee['name'] ?? null,
                'role' => $invitee['role'],
                'invited_by' => auth()->id(),
                'token' => Str::random(64),
                'expires_at' => now()->addDays(7),
            ]);

            (new AnonymousNotifiable)
                ->route('mail', $invitation->email)
                ->notify(new InvitationNotification($invitation));

            $count++;
        }

        $message = $count === 1
            ? "Invitation sent to {$request->validated('invitees.0.email')}."
            : "{$count} invitations sent successfully.";

        ActivityLog::log('sent_invitations', "Sent {$count} invitation(s)");

        return redirect()->route('admin.users.index')->with('success', $message);
    }

    public function invalidateInvitation(Invitation $invitation): RedirectResponse
    {
        abort_unless($invitation->isAccepted() === false, 404);

        // Mark as expired by setting expires_at to the past
        $invitation->update(['expires_at' => now()->subSecond()]);

        return redirect()->route('admin.users.index')->with('success', 'Invitation invalidated.');
    }

    public function pendingInvitations(): JsonResponse
    {
        $invitations = Invitation::with('inviter')->pending()->orderByDesc('created_at')->get();

        $items = $invitations->map(fn (Invitation $invitation) => [
            'id' => $invitation->id,
            'display_name' => $invitation->name ?? $invitation->email,
            'email' => $invitation->email,
            'role' => $invitation->role->value,
            'inviter_name' => $invitation->inviter->name,
            'has_name' => $invitation->name !== null,
            'expires_at' => $invitation->expires_at->format('M j, Y'),
            'invalidate_url' => route('admin.invitations.invalidate', $invitation),
        ]);

        return response()->json([
            'count' => $invitations->count(),
            'items' => $items,
        ]);
    }
}
