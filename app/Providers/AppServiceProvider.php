<?php

namespace App\Providers;

use App\Enums\UserRole;
use App\Models\User;
use App\Support\SiteSettings;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\View as BladeView;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SiteSettings::class, function () {
            return new SiteSettings;
        });
    }

    public function boot(): void
    {
        // Apply system timezone from site settings
        $settings = app(SiteSettings::class);
        $timezone = $settings->get('timezone', 'Asia/Manila');
        date_default_timezone_set($timezone);
        config(['app.timezone' => $timezone]);

        Gate::define('admin', fn (User $user) => $user->role === UserRole::Admin);
        Gate::define('teacher', fn (User $user) => $user->role === UserRole::Teacher);
        Gate::define('student', fn (User $user) => $user->role === UserRole::Student);

        View::composer('*', function (BladeView $view) use ($settings): void {
            $view->with([
                'institutionName' => $settings->get('institution_name', 'Attendify'),
                'institutionLogo' => $settings->get('institution_logo') ?: asset('assets/attendify.png'),
                'landingBanner' => $settings->get('landing_banner'),
                'institutionMission' => $settings->get('mission'),
                'institutionVision' => $settings->get('vision'),
            ]);
        });
    }
}
