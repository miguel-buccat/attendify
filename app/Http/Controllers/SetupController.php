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
            'hasAdmin' => $this->hasAdminAccount(),
            'siteSettings' => $siteSettings,
        ]);
    }

    public function storeAdmin(Request $request, SiteSettings $siteSettings): RedirectResponse
    {
        if ($this->isSetupComplete($siteSettings)) {
            return redirect()->route('landing');
        }

        if ($this->hasAdminAccount()) {
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

        if (! $this->hasAdminAccount()) {
            return redirect()->route('new.setup');
        }

        $validated = $request->validate([
            'institution_name' => ['required', 'string', 'max:255'],
            'institution_logo' => ['required', 'image', 'max:2048'],
            'landing_banner' => ['required', 'image', 'max:4096'],
        ]);

        $logoPath = $request->file('institution_logo')->store('site-settings', 'public');
        $bannerPath = $request->file('landing_banner')->store('site-settings', 'public');

        if (! app()->runningUnitTests() && ! is_link(public_path('storage')) && ! is_dir(public_path('storage'))) {
            Artisan::call('storage:link');
        }

        $logoUrl = Storage::disk('public')->url($logoPath);
        $bannerUrl = Storage::disk('public')->url($bannerPath);

        $siteSettings->set('institution_name', $validated['institution_name']);
        $siteSettings->set('institution_logo', url($logoUrl));
        $siteSettings->set('landing_banner', url($bannerUrl));

        return redirect()->route('landing');
    }

    private function hasAdminAccount(): bool
    {
        return User::query()
            ->where('role', UserRole::Admin->value)
            ->exists();
    }

    private function isSetupComplete(SiteSettings $siteSettings): bool
    {
        return $this->hasAdminAccount()
            && (bool) $siteSettings->get('institution_logo')
            && (bool) $siteSettings->get('landing_banner');
    }
}
