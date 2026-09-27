<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Student;
use App\Models\User;

class StudentPolicy
{
    /**
     * Determine whether the user can view a student's full record, which
     * exposes date of birth, blood group, home address and guardian contacts.
     */
    public function view(User $user, Student $student): bool
    {
        if (in_array($user->role, [UserRole::Admin->value, UserRole::Teacher->value], true)) {
            return true;
        }

        if ($user->role === UserRole::Parent->value) {
            return $user->parentStudents()->whereKey($student->getKey())->exists();
        }

        return $student->user_id === $user->id;
    }

    /**
     * Determine whether the user can print a student's ID card.
     */
    public function printIdCard(User $user, Student $student): bool
    {
        return $this->view($user, $student);
    }
}
