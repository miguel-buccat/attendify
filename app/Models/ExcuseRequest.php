<?php

namespace App\Models;

use App\Enums\ExcuseRequestStatus;
use Database\Factories\ExcuseRequestFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['student_id', 'class_id', 'excuse_date', 'reason', 'document_path', 'status', 'reviewed_by', 'reviewed_at', 'reviewer_notes'])]
class ExcuseRequest extends Model
{
    /** @use HasFactory<ExcuseRequestFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'excuse_date' => 'date',
            'status' => ExcuseRequestStatus::class,
            'reviewed_at' => 'datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
