<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Console\Command\Command as SymfonyCommand;

#[Signature('app:dev-reset-site')]
#[Description('Reset setup data for local development (images, site config, and database).')]
class DevResetSite extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (! App::environment(['local', 'testing'])) {
            $this->components->error('This command is only available in local/testing environments.');

            return SymfonyCommand::FAILURE;
        }

        if (! $this->confirm('This will delete setup images, site settings, and run migrate:fresh. Continue?', true)) {
            $this->components->warn('Cancelled.');

            return SymfonyCommand::INVALID;
        }

        Storage::disk('public')->deleteDirectory('site-settings');
        Storage::disk('local')->deleteDirectory('site-settings');

        File::delete(storage_path('app/site-settings.json'));

        $this->components->info('Deleted setup images and site settings JSON.');

        $this->components->task('Running migrate:fresh', function (): bool {
            return Artisan::call('migrate:fresh', ['--force' => true]) === SymfonyCommand::SUCCESS;
        });

        $this->components->info('Site reset complete.');

        return SymfonyCommand::SUCCESS;
    }
}
