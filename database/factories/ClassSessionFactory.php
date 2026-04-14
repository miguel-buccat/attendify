<?php

namespace Database\Factories;

use App\Enums\SessionModality;
use App\Enums\SessionStatus;
use App\Models\ClassSession;
use App\Models\SchoolClass;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<ClassSession> */
class ClassSessionFactory extends Factory
{
    protected $model = ClassSession::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        $startTime = now()->addDays(fake()->numberBetween(1, 14))->setTime(fake()->numberBetween(8, 16), 0);
        $endTime = $startTime->copy()->addHours(fake()->numberBetween(1, 3));

        return [
            'class_id' => SchoolClass::factory(),
            'modality' => SessionModality::Onsite,
            'location' => fake()->optional()->word(),
            'start_time' => $startTime,
            'end_time' => $endTime,
            'grace_period_minutes' => 15,
            'qr_token' => Str::random(64),
            'qr_expires_at' => $endTime->copy()->addMinutes(15),
            'status' => SessionStatus::Scheduled,
        ];
    }

    public function active(): static
    {
        return $this->state(function () {
            $startTime = now()->subMinutes(30);
            $endTime = now()->addHours(1);

            return [
                'status' => SessionStatus::Active,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'qr_expires_at' => $endTime->copy()->addMinutes(15),
            ];
        });
    }

    public function completed(): static
    {
        return $this->state(function () {
            $startTime = now()->subHours(3);
            $endTime = now()->subHour();

            return [
                'status' => SessionStatus::Completed,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'qr_expires_at' => $endTime->copy()->addMinutes(15),
            ];
        });
    }

    public function cancelled(): static
    {
        return $this->state(['status' => SessionStatus::Cancelled]);
    }

    public function onsite(): static
    {
        return $this->state([
            'modality' => SessionModality::Onsite,
            'location' => fake()->word(),
        ]);
    }

    public function online(): static
    {
        return $this->state([
            'modality' => SessionModality::Online,
            'location' => 'https://meet.example.com/'.Str::random(8),
        ]);
    }
}
