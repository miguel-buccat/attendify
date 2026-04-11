<?php

namespace App\Policies;

use App\Models\ExcuseRequest;
use App\Models\User;

class ExcuseRequestPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role->value === 'Student' || $user->role->value === 'Teacher';
    }

    public function create(User $user): bool
    {
        return $user->role->value === 'Student';
    }

    public function review(User $user, ExcuseRequest $excuseRequest): bool
    {
        if ($user->role->value !== 'Teacher') {
            return false;
        }

        // Only the teacher who owns the class can review
        return $excuseRequest->schoolClass->teacher_id === $user->id;
    }
}
