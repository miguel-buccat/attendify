<?php

namespace App\Http\Requests\Teacher;

use App\Enums\SessionModality;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BulkScheduleSessionRequest extends FormRequest
{
    protected $errorBag = 'preschedule';

    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'days' => ['required', 'array', 'min:1'],
            'days.*' => ['required', 'string', Rule::in(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'])],
            'modality' => ['required', Rule::enum(SessionModality::class)],
            'location' => ['nullable', 'string', 'max:255'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'grace_period_minutes' => ['nullable', 'integer', 'min:1', 'max:60'],
            'interval_weeks' => ['sometimes', 'integer', 'min:1', 'max:4'],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['required', 'date', 'after:start_date'],
        ];
    }
}
