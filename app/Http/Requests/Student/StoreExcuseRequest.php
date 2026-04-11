<?php

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreExcuseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        $enrolledClassIds = $this->user()->enrolledClasses()->pluck('school_classes.id');

        return [
            'class_id' => ['required', Rule::in($enrolledClassIds)],
            'excuse_date' => ['required', 'date', 'after_or_equal:today'],
            'reason' => ['required', 'string', 'max:1000'],
            'document' => ['required', 'file', 'mimes:pdf', 'max:5120'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'class_id.required' => 'Please select a class.',
            'class_id.in' => 'You are not enrolled in the selected class.',
        ];
    }
}
