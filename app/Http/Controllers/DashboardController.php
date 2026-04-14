<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Student\StudentDashboardController;
use App\Http\Controllers\Teacher\TeacherDashboardController;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        return match ($user->role) {
            UserRole::Admin => app(AdminDashboardController::class)->index(),
            UserRole::Teacher => app(TeacherDashboardController::class)->index(),
            UserRole::Student => app(StudentDashboardController::class)->index(),
        };
    }
}
