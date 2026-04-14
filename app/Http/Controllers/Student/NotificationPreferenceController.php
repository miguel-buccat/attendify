<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationPreferenceController extends Controller
{
    public function edit(): View
    {
        $user = auth()->user();
        $preferences = $user->notification_preferences ?? User::defaultNotificationPreferences();

        return view('student.notifications.edit', compact('preferences'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'session_started' => ['nullable', 'boolean'],
            'weekly_summary' => ['nullable', 'boolean'],
            'absence_alert' => ['nullable', 'boolean'],
            'excuse_updates' => ['nullable', 'boolean'],
        ]);

        $preferences = [
            'session_started' => (bool) ($validated['session_started'] ?? false),
            'weekly_summary' => (bool) ($validated['weekly_summary'] ?? false),
            'absence_alert' => (bool) ($validated['absence_alert'] ?? false),
            'excuse_updates' => (bool) ($validated['excuse_updates'] ?? false),
        ];

        auth()->user()->update(['notification_preferences' => $preferences]);

        return redirect()->route('student.notifications.edit')
            ->with('success', 'Notification preferences updated.');
    }
}
