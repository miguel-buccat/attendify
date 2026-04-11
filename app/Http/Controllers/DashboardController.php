<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        $view = match ($user->role) {
            UserRole::Admin => 'dashboard.admin',
            UserRole::Teacher => 'dashboard.teacher',
            UserRole::Student => 'dashboard.student',
        };

        return view($view, ['user' => $user]);
    }
}
