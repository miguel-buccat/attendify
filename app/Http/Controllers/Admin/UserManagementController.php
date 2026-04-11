<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\InviteUserRequest;
use App\Models\Invitation;
use App\Models\User;
use App\Notifications\Auth\InvitationNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Str;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    public function index(): View
    {
        $users = User::orderBy('name')->get();
        $invitations = Invitation::with('inviter')->pending()->orderByDesc('created_at')->get();

        return view('admin.users.index', compact('users', 'invitations'));
    }

    public function invite(): View
    {
        return view('admin.users.invite');
    }

    public function sendInvitation(InviteUserRequest $request): RedirectResponse
    {
        $invitation = Invitation::create([
            'email' => $request->validated('email'),
            'role' => $request->validated('role'),
            'invited_by' => auth()->id(),
            'token' => Str::random(64),
            'expires_at' => now()->addDays(7),
        ]);

        (new AnonymousNotifiable)
            ->route('mail', $invitation->email)
            ->notify(new InvitationNotification($invitation));

        return redirect()->route('admin.users.index')
            ->with('success', "Invitation sent to {$invitation->email}.");
    }
}
