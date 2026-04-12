<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use App\Models\User;
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
            return view('invitation.invalid', ['reason' => 'accepted']);
        }

        if ($invitation->isExpired()) {
            return view('invitation.invalid', ['reason' => 'expired']);
        }

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];

        if ($invitation->role === \App\Enums\UserRole::Student) {
            $rules['guardian_email'] = ['required', 'email', 'max:255'];
            $rules['guardian_phone'] = ['required', 'string', 'max:30'];
        }

        $validated = $request->validate($rules);

        if (User::where('email', $invitation->email)->exists()) {
            return back()->withErrors(['email' => 'This email address has already been registered.']);
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $invitation->email,
            'password' => \Illuminate\Support\Facades\Hash::make($validated['password']),
            'role' => $invitation->role,
            'email_verified_at' => now(),
            'guardian_email' => $validated['guardian_email'] ?? null,
            'guardian_phone' => $validated['guardian_phone'] ?? null,
        ]);

        $invitation->update(['accepted_at' => now()]);

        Auth::login($user);

        return redirect()->route('dashboard');
    }
}
