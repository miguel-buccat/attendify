<?php

namespace App\Http\Requests\Teacher;

use App\Enums\ExcuseRequestStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReviewExcuseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in([ExcuseRequestStatus::Acknowledged->value, ExcuseRequestStatus::Rejected->value])],
            'reviewer_notes' => ['nullable', 'string', 'max:500'],
        ];
    }
}
