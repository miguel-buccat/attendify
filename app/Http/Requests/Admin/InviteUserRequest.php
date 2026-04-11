<?php

namespace App\Http\Requests\Admin;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class InviteUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email'),
                Rule::unique('invitations', 'email')->where(
                    fn ($query) => $query->whereNull('accepted_at')->where('expires_at', '>', now())
                ),
            ],
            'role' => ['required', new Enum(UserRole::class), Rule::in([UserRole::Teacher->value, UserRole::Student->value])],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.unique' => 'This email address already has an account or a pending invitation.',
        ];
    }
}
