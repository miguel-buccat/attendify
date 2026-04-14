<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\SchoolClass;
use App\Models\User;

class SchoolClassPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role === UserRole::Teacher || $user->role === UserRole::Student;
    }

    public function view(User $user, SchoolClass $schoolClass): bool
    {
        if ($user->role === UserRole::Teacher) {
            return $schoolClass->teacher_id === $user->id;
        }

        if ($user->role === UserRole::Student) {
            return $schoolClass->students()->where('student_id', $user->id)->exists();
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->role === UserRole::Teacher;
    }

    public function update(User $user, SchoolClass $schoolClass): bool
    {
        return $schoolClass->teacher_id === $user->id;
    }

    public function archive(User $user, SchoolClass $schoolClass): bool
    {
        return $schoolClass->teacher_id === $user->id;
    }

    public function enroll(User $user, SchoolClass $schoolClass): bool
    {
        return $schoolClass->teacher_id === $user->id;
    }

    public function unenroll(User $user, SchoolClass $schoolClass): bool
    {
        return $schoolClass->teacher_id === $user->id;
    }
}
