<?php

namespace Database\Factories;

use App\Enums\ClassStatus;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<SchoolClass> */
class SchoolClassFactory extends Factory
{
    protected $model = SchoolClass::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'teacher_id' => User::factory()->teacher(),
            'name' => fake()->sentence(3),
            'description' => fake()->optional()->paragraph(),
            'section' => fake()->optional()->lexify('Section ?'),
            'status' => ClassStatus::Active,
        ];
    }

    public function archived(): static
    {
        return $this->state(['status' => ClassStatus::Archived]);
    }
}
