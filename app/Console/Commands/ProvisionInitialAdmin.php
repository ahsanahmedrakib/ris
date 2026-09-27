<?php

namespace App\Console\Commands;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class ProvisionInitialAdmin extends Command
{
    protected $signature = 'app:provision-admin
                            {--name= : Display name for the administrator}
                            {--username= : Login username}
                            {--email= : Login email address}
                            {--password= : Password; prefer DEFAULT_ADMIN_PASSWORD or the interactive prompt}';

    protected $description = 'Create the first administrator account for a fresh installation';

    public function handle(): int
    {
        // Provisioning is deliberately explicit. It used to run inside the login
        // request, which meant any anonymous visitor could silently create a
        // known-password admin the moment the users table was empty.
        if (User::where('role', UserRole::Admin->value)->exists()) {
            $this->warn('An administrator already exists. Nothing was changed.');

            return self::SUCCESS;
        }

        $name = $this->option('name') ?: (string) config('auth.default_admin.name');
        $username = $this->option('username') ?: (string) config('auth.default_admin.username');
        $email = $this->option('email') ?: (string) config('auth.default_admin.email');
        $password = (string) ($this->option('password') ?: config('auth.default_admin.password'));

        $validator = Validator::make([
            'name' => $name,
            'username' => $username,
            'email' => $email,
            'password' => $password,
        ], [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => ['required', 'string', Password::min(12)->letters()->numbers()->symbols()],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            $this->line('Set DEFAULT_ADMIN_PASSWORD, or pass --password with at least 12 mixed characters.');

            return self::FAILURE;
        }

        User::create([
            'name' => $name,
            'username' => $username,
            'email' => $email,
            'password' => Hash::make($password),
            'role' => UserRole::Admin->value,
            'is_active' => true,
        ]);

        $this->info(sprintf('Administrator "%s" (%s) created.', $username, $email));

        return self::SUCCESS;
    }
}
