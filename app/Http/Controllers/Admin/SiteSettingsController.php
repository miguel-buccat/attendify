<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Support\SiteSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SiteSettingsController extends Controller
{
    public function edit(SiteSettings $siteSettings): View
    {
        return view('admin.settings', [
            'settings' => $siteSettings->getAll(),
        ]);
    }

    public function update(Request $request, SiteSettings $siteSettings): RedirectResponse
    {
        $validated = $request->validate([
            'institution_name' => ['required', 'string', 'max:255'],
            'timezone' => ['required', 'string', 'timezone:all'],
            'institution_logo' => ['nullable', 'image', 'mimes:jpeg,jpg,png,gif,webp', 'max:2048'],
            'landing_banner' => ['nullable', 'image', 'mimes:jpeg,jpg,png,gif,webp', 'max:4096'],
            'mission' => ['nullable', 'string', 'max:1000'],
            'vision' => ['nullable', 'string', 'max:1000'],
        ]);

        $siteSettings->set('institution_name', $validated['institution_name']);
        $siteSettings->set('timezone', $validated['timezone']);
        $siteSettings->set('mission', $validated['mission'] ?? null);
        $siteSettings->set('vision', $validated['vision'] ?? null);

        if ($request->hasFile('institution_logo')) {
            $logoPath = $request->file('institution_logo')->store('site-settings', 'public');
            $siteSettings->set('institution_logo', url(Storage::disk('public')->url($logoPath)));
        }

        if ($request->hasFile('landing_banner')) {
            $bannerPath = $request->file('landing_banner')->store('site-settings', 'public');
            $siteSettings->set('landing_banner', url(Storage::disk('public')->url($bannerPath)));
        }

        ActivityLog::log('updated_settings', 'Updated site settings');

        return redirect()->route('admin.settings.edit')->with('success', 'Site settings updated.');
    }
}
