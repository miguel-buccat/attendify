<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\User;
use App\Support\SiteSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class SetupController extends Controller
{
    public function newIndex(SiteSettings $siteSettings): View|RedirectResponse
    {
        if ($this->isSetupComplete($siteSettings)) {
            return redirect()->route('landing');
        }

        return view('new.index');
    }

    public function index(SiteSettings $siteSettings): View|RedirectResponse
    {
        if ($this->isSetupComplete($siteSettings)) {
            return redirect()->route('landing');
        }

        return view('new.setup', [
            'hasAdmin' => User::admin()->exists(),
            'siteSettings' => $siteSettings,
        ]);
    }

    public function storeAdmin(Request $request, SiteSettings $siteSettings): RedirectResponse
    {
        if ($this->isSetupComplete($siteSettings)) {
            return redirect()->route('landing');
        }

        if (User::admin()->exists()) {
            return redirect()->route('new.setup');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        User::query()->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'email_verified_at' => now(),
            'role' => UserRole::Admin->value,
        ]);

        return redirect()
            ->route('new.setup')
            ->with('status', 'Admin account created. Continue to site settings.');
    }

    public function storeSettings(Request $request, SiteSettings $siteSettings): RedirectResponse
    {
        if ($this->isSetupComplete($siteSettings)) {
            return redirect()->route('landing');
        }

        if (! User::admin()->exists()) {
            return redirect()->route('new.setup');
        }

        $validated = $request->validate([
            'institution_name' => ['required', 'string', 'max:255'],
            'timezone' => ['required', 'string', 'timezone:all'],
            'institution_logo' => ['required', 'image', 'mimes:jpeg,jpg,png,gif,webp', 'max:2048'],
            'landing_banner' => ['required', 'image', 'mimes:jpeg,jpg,png,gif,webp', 'max:4096'],
            'mission' => ['nullable', 'string', 'max:1000'],
            'vision' => ['nullable', 'string', 'max:1000'],
        ]);

        $logoPath = $request->file('institution_logo')->store('site-settings', 'public');
        $bannerPath = $request->file('landing_banner')->store('site-settings', 'public');

        if (! app()->runningUnitTests() && ! is_link(public_path('storage')) && ! is_dir(public_path('storage'))) {
            Artisan::call('storage:link');
        }

        $siteSettings->set('institution_name', $validated['institution_name']);
        $siteSettings->set('timezone', $validated['timezone']);
        $siteSettings->set('institution_logo', url(Storage::disk('public')->url($logoPath)));
        $siteSettings->set('landing_banner', url(Storage::disk('public')->url($bannerPath)));
        $siteSettings->set('mission', $validated['mission'] ?? null);
        $siteSettings->set('vision', $validated['vision'] ?? null);

        return redirect()->route('landing');
    }

    private function isSetupComplete(SiteSettings $siteSettings): bool
    {
        return User::admin()->exists()
            && (bool) $siteSettings->get('institution_logo')
            && (bool) $siteSettings->get('landing_banner');
    }
}
