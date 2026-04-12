<?php

namespace App\Http\Requests\Teacher;

use App\Enums\SessionModality;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StartSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'modality' => ['required', Rule::enum(SessionModality::class)],
            'location' => ['nullable', 'string', 'max:255'],
            'start_time' => ['required', 'date', 'after_or_equal:now'],
            'end_time' => ['required', 'date', 'after:start_time'],
            'grace_period_minutes' => ['nullable', 'integer', 'min:1', 'max:60'],
            'recurrence_pattern' => ['nullable', 'string', Rule::in(['weekly', 'biweekly'])],
            'recurrence_end_date' => ['nullable', 'required_with:recurrence_pattern', 'date', 'after:start_time'],
        ];
    }
}
