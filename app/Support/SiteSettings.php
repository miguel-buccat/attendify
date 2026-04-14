<?php

namespace App\Support;

use Illuminate\Support\Facades\File;
use InvalidArgumentException;
use JsonException;
use RuntimeException;

class SiteSettings
{
    /**
     * Define all supported site settings and their default values here.
     *
     * @var array<string, mixed>
     */
    private const FIELDS = [
        'institution_name' => 'Attendify',
        'institution_logo' => null,
        'landing_banner' => null,
        'timezone' => 'Asia/Manila',
        'mission' => null,
        'vision' => null,
    ];

    /**
     * @return array<string, mixed>
     */
    public function getAll(): array
    {
        return array_replace(self::FIELDS, $this->readFromFile());
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $settings = $this->getAll();

        return $settings[$key] ?? $default;
    }

    public function set(string $key, mixed $value): void
    {
        if (! array_key_exists($key, self::FIELDS)) {
            throw new InvalidArgumentException("Unknown site setting key [{$key}]. Add it to SiteSettings::FIELDS first.");
        }

        $settings = $this->getAll();
        $settings[$key] = $value;

        $this->writeToFile($settings);
    }

    /**
     * @return array<string, mixed>
     */
    private function readFromFile(): array
    {
        $this->ensureFileExists();

        $raw = trim(File::get($this->path()));

        if ($raw === '') {
            return [];
        }

        try {
            $decoded = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new RuntimeException('Invalid JSON in site settings file: '.$this->path(), previous: $exception);
        }

        if (! is_array($decoded)) {
            throw new RuntimeException('Site settings JSON must decode to an object.');
        }

        return $decoded;
    }

    /**
     * @param  array<string, mixed>  $settings
     */
    private function writeToFile(array $settings): void
    {
        $this->ensureDirectoryExists();

        try {
            $payload = json_encode($settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new RuntimeException('Unable to encode site settings.', previous: $exception);
        }

        File::put($this->path(), $payload.PHP_EOL, lock: true);
    }

    private function ensureFileExists(): void
    {
        if (! File::exists($this->path())) {
            $this->writeToFile(self::FIELDS);
        }
    }

    private function ensureDirectoryExists(): void
    {
        $directory = dirname($this->path());

        if (! File::isDirectory($directory)) {
            File::ensureDirectoryExists($directory);
        }
    }

    private function path(): string
    {
        $path = config('site.settings_file', storage_path('app/site-settings.json'));

        if (! is_string($path) || $path === '') {
            throw new RuntimeException('Invalid site settings file path configuration.');
        }

        return $path;
    }
}
