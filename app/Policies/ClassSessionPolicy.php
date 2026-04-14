<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\ClassSession;
use App\Models\User;

class ClassSessionPolicy
{
    public function view(User $user, ClassSession $session): bool
    {
        return $session->schoolClass->teacher_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->role === UserRole::Teacher;
    }

    public function start(User $user, ClassSession $session): bool
    {
        return $session->schoolClass->teacher_id === $user->id;
    }

    public function complete(User $user, ClassSession $session): bool
    {
        return $session->schoolClass->teacher_id === $user->id;
    }

    public function cancel(User $user, ClassSession $session): bool
    {
        return $session->schoolClass->teacher_id === $user->id;
    }
}
