<?php

namespace App\Features\Auth\Services;

use App\Enums\UserRole;
use App\Models\User;

class DefaultAdminService
{
    /**
     * Bootstrap the super administrator when the users table is empty.
     */
    public function ensureDefaultAdmin(): void
    {
        if (User::count() > 0) {
            return;
        }

        User::create([
            'name' => config('auth.default_admin.name'),
            'username' => config('auth.default_admin.username'),
            'email' => config('auth.default_admin.email'),
            'password' => config('auth.default_admin.password'),
            'role' => UserRole::Admin->value,
            'is_active' => true,
        ]);
    }
}
