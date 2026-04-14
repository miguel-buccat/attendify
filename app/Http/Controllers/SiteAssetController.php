<?php

namespace App\Http\Controllers;

use App\Support\SiteSettings;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class SiteAssetController extends Controller
{
    public function show(string $key, SiteSettings $siteSettings): Response
    {
        if (! in_array($key, ['institution_logo', 'landing_banner'], true)) {
            abort(404);
        }

        $path = $siteSettings->get($key);

        if (! is_string($path) || $path === '') {
            abort(404);
        }

        if (! Storage::disk('local')->exists($path)) {
            abort(404);
        }

        return response()->file(Storage::disk('local')->path($path));
    }
}
