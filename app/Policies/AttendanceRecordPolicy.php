<?php

namespace App\Policies;

use App\Models\AttendanceRecord;
use App\Models\ClassSession;
use App\Models\User;

class AttendanceRecordPolicy
{
    public function viewAny(User $user, ClassSession $session): bool
    {
        return $session->schoolClass->teacher_id === $user->id;
    }

    public function update(User $user, AttendanceRecord $record): bool
    {
        return $record->classSession->schoolClass->teacher_id === $user->id;
    }
}
