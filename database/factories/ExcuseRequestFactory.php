<?php

namespace Database\Factories;

use App\Enums\ExcuseRequestStatus;
use App\Models\ExcuseRequest;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<ExcuseRequest> */
class ExcuseRequestFactory extends Factory
{
    protected $model = ExcuseRequest::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'student_id' => User::factory()->student(),
            'class_id' => SchoolClass::factory(),
            'excuse_date' => now()->addDays(fake()->numberBetween(1, 14)),
            'reason' => fake()->sentence(),
            'document_path' => 'excuse-documents/test-document.pdf',
            'status' => ExcuseRequestStatus::Pending,
        ];
    }

    public function acknowledged(): static
    {
        return $this->state([
            'status' => ExcuseRequestStatus::Acknowledged,
            'reviewed_at' => now(),
        ]);
    }

    public function rejected(): static
    {
        return $this->state([
            'status' => ExcuseRequestStatus::Rejected,
            'reviewed_at' => now(),
        ]);
    }
}
