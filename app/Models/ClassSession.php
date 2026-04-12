<?php

namespace App\Models;

use App\Enums\SessionModality;
use App\Enums\SessionStatus;
use Database\Factories\ClassSessionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['class_id', 'modality', 'location', 'start_time', 'end_time', 'grace_period_minutes', 'qr_token', 'qr_expires_at', 'status', 'recurrence_pattern', 'recurrence_end_date', 'recurrence_group_id', 'cancellation_reason'])]
class ClassSession extends Model
{
    /** @use HasFactory<ClassSessionFactory> */
    use HasFactory, HasUlids;

    protected function casts(): array
    {
        return [
            'modality' => SessionModality::class,
            'status' => SessionStatus::class,
            'start_time' => 'datetime',
            'end_time' => 'datetime',
            'qr_expires_at' => 'datetime',
            'grace_period_minutes' => 'integer',
            'recurrence_end_date' => 'date',
        ];
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function attendanceRecords(): HasMany
    {
        return $this->hasMany(AttendanceRecord::class);
    }

    public function isActive(): bool
    {
        return $this->status === SessionStatus::Active;
    }

    public function isScheduled(): bool
    {
        return $this->status === SessionStatus::Scheduled;
    }

    public function isCompleted(): bool
    {
        return $this->status === SessionStatus::Completed;
    }

    public function isRecurring(): bool
    {
        return $this->recurrence_group_id !== null;
    }

    public function recurringGroup(): HasMany
    {
        return $this->hasMany(self::class, 'recurrence_group_id', 'recurrence_group_id');
    }
}
