<?php

namespace Database\Factories;

use App\Enums\AttendanceMarkedBy;
use App\Enums\AttendanceStatus;
use App\Models\AttendanceRecord;
use App\Models\ClassSession;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<AttendanceRecord> */
class AttendanceRecordFactory extends Factory
{
    protected $model = AttendanceRecord::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'class_session_id' => ClassSession::factory(),
            'student_id' => User::factory()->student(),
            'status' => AttendanceStatus::Present,
            'scanned_at' => now(),
            'marked_by' => AttendanceMarkedBy::System,
        ];
    }

    public function late(): static
    {
        return $this->state([
            'status' => AttendanceStatus::Late,
        ]);
    }

    public function absent(): static
    {
        return $this->state([
            'status' => AttendanceStatus::Absent,
            'scanned_at' => null,
        ]);
    }

    public function excused(): static
    {
        return $this->state([
            'status' => AttendanceStatus::Excused,
            'marked_by' => AttendanceMarkedBy::Teacher,
        ]);
    }
}
