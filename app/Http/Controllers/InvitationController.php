<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\Invitation;
use App\Models\User;
use App\Notifications\NewUserRegisteredNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class InvitationController extends Controller
{
    public function show(string $token): View|RedirectResponse
    {
        $invitation = Invitation::where('token', $token)->firstOrFail();

        if ($invitation->isAccepted()) {
            return view('invitation.invalid', ['reason' => 'accepted']);
        }

        if ($invitation->isExpired()) {
            return view('invitation.invalid', ['reason' => 'expired']);
        }

        return view('invitation.accept', compact('invitation'));
    }

    public function store(Request $request, string $token): RedirectResponse
    {
        $invitation = Invitation::where('token', $token)->firstOrFail();

        if ($invitation->isAccepted()) {
            return redirect()->route('invitation.accept', $token)->withErrors(['token' => 'This invitation has already been accepted.']);
        }

        if ($invitation->isExpired()) {
            return redirect()->route('invitation.accept', $token)->withErrors(['token' => 'This invitation has expired.']);
        }

        $rules = [
            'name' => $invitation->name ? ['sometimes', 'string', 'max:255'] : ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];

        if ($invitation->role === UserRole::Student) {
            $rules['guardian_name'] = ['required', 'string', 'max:255'];
            $rules['guardian_email'] = ['required', 'email', 'max:255'];
        }

        $validated = $request->validate($rules);

        if (User::where('email', $invitation->email)->exists()) {
            return back()->withErrors(['email' => 'This email address has already been registered.']);
        }

        $user = User::create([
            'name' => $validated['name'] ?? $invitation->name,
            'email' => $invitation->email,
            'password' => Hash::make($validated['password']),
            'role' => $invitation->role,
            'email_verified_at' => now(),
            'guardian_name' => $validated['guardian_name'] ?? null,
            'guardian_email' => $validated['guardian_email'] ?? null,
        ]);

        $invitation->update(['accepted_at' => now()]);

        User::admin()->get()->each(
            fn (User $admin) => $admin->notify(new NewUserRegisteredNotification($user))
        );

        Auth::login($user);

        return redirect()->route('dashboard');
    }
}
