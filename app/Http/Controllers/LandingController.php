<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LandingController extends Controller
{
    public function index(): View|RedirectResponse
    {
        if (! User::admin()->exists()) {
            return redirect()->route('new.index');
        }

        return view('landing');
    }
}
