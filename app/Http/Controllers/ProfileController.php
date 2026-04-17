<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show(User $user): View
    {
        return view('profile.show', ['profileUser' => $user]);
    }

    public function edit(): View
    {
        return view('profile.edit');
    }

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $user = $request->user();
        $data = [];

        if ($request->hasFile('avatar')) {
            if ($user->avatar_path) {
                Storage::disk('public')->delete($user->avatar_path);
            }
            $ext = $request->file('avatar')->getClientOriginalExtension();
            $data['avatar_path'] = $request->file('avatar')->storeAs(
                'avatars',
                $user->id.'_'.now()->timestamp.'.'.$ext,
                'public'
            );
        }

        if ($request->hasFile('banner')) {
            if ($user->banner_path) {
                Storage::disk('public')->delete($user->banner_path);
            }
            $ext = $request->file('banner')->getClientOriginalExtension();
            $data['banner_path'] = $request->file('banner')->storeAs(
                'banners',
                $user->id.'_'.now()->timestamp.'.'.$ext,
                'public'
            );
        }

        $user->update($data);

        return redirect()->back()
            ->with('success', 'Profile updated successfully.');
    }
}
