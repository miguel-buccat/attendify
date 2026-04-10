<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LandingController extends Controller
{
    public function index(): View|RedirectResponse
    {
        if (! $this->hasAdminAccount()) {
            return redirect()->route('new.index');
        }

        return view('landing');
    }

    private function hasAdminAccount(): bool
    {
        return User::query()
            ->where('role', UserRole::Admin->value)
            ->exists();
    }
}
