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
            'invitees' => ['required', 'array', 'min:1'],
            'invitees.*.email' => [
                'required',
                'email',
                Rule::unique('users', 'email'),
                Rule::unique('invitations', 'email')->where(
                    fn ($query) => $query->whereNull('accepted_at')->where('expires_at', '>', now())
                ),
            ],
            'invitees.*.role' => ['required', new Enum(UserRole::class)],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'invitees.*.email.unique' => 'This email address already has an account or a pending invitation.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        $attributes = [];

        foreach ($this->input('invitees', []) as $index => $invitee) {
            $num = $index + 1;
            $attributes["invitees.{$index}.email"] = "email #{$num}";
            $attributes["invitees.{$index}.role"] = "role #{$num}";
        }

        return $attributes;
    }
}
