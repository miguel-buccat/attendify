<?php

use App\Console\Commands\SendWeeklyReports;
use App\Jobs\ExpireStaleInvitations;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::job(new ExpireStaleInvitations)->daily();
Schedule::command(SendWeeklyReports::class)->weeklyOn(0, '18:00');
