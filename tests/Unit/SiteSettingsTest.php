<?php

use App\Support\SiteSettings;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    config()->set('site.settings_file', storage_path('framework/testing/site-settings-test.json'));

    File::delete(config('site.settings_file'));
    app()->forgetInstance(SiteSettings::class);
});

it('returns all settings and creates file when missing', function () {
    $settings = app(SiteSettings::class);

    $all = $settings->getAll();

    expect($all['institution_name'])->toBe('Attendify');
    expect($all['institution_logo'])->toBeNull();
    expect(File::exists(config('site.settings_file')))->toBeTrue();
});

it('gets values from file-backed settings', function () {
    $settings = app(SiteSettings::class);

    expect($settings->get('institution_name'))->toBe('Attendify');
    expect($settings->get('institution_logo'))->toBeNull();
});

it('persists values to json and reloads them', function () {
    $settings = app(SiteSettings::class);

    $settings->set('institution_name', 'Erovoutika Robotics and Automation Solutions');
    $settings->set('institution_logo', '/storage/logos/institution.png');

    app()->forgetInstance(SiteSettings::class);

    $freshSettings = app(SiteSettings::class);

    expect($freshSettings->get('institution_name'))->toBe('Erovoutika Robotics and Automation Solutions');
    expect($freshSettings->get('institution_logo'))->toBe('/storage/logos/institution.png');
});

it('rejects unknown keys', function () {
    $settings = app(SiteSettings::class);

    expect(fn () => $settings->set('unknown_key', 'value'))
        ->toThrow(\InvalidArgumentException::class);
});
