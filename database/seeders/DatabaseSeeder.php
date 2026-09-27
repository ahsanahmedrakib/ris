<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\ClassRoom;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // The seeder refuses to invent a password. Set SEED_ADMIN_PASSWORD (or
        // DEFAULT_ADMIN_PASSWORD) before seeding, otherwise it aborts rather
        // than leaving a "password"/"password" account behind.
        $password = (string) (env('SEED_ADMIN_PASSWORD') ?: config('auth.default_admin.password'));

        $validator = Validator::make(['password' => $password], [
            'password' => ['required', 'string', 'min:12'],
        ]);

        if ($validator->fails()) {
            $this->command?->error('SEED_ADMIN_PASSWORD must be set to at least 12 characters before seeding.');

            throw new \RuntimeException('Refusing to seed a default administrator with a weak or missing password.');
        }

        $admin = User::firstOrCreate(
            ['email' => 'admin@ris.edu.bd'],
            [
                'name' => 'অ্যাডমিন',
                'password' => Hash::make($password),
                'role' => 'admin',
                'phone' => '01700000000',
                'is_active' => true,
            ]
        );

        // Academic Year
        $year = AcademicYear::firstOrCreate(
            ['name' => '২০২৫-২০২৬'],
            [
                'start_date' => '2025-01-01',
                'end_date' => '2025-12-31',
                'is_current' => true,
            ]
        );

        // Classes (school serves Play, Nursery, and Classes 1-5)
        $classNames = ['প্লে', 'নার্সারি', 'কেজি', '১ম', '২য়', '৩য়', '৪র্থ', '৫ম'];
        foreach ($classNames as $name) {
            ClassRoom::firstOrCreate(
                ['name' => $name, 'section' => 'ক', 'academic_year_id' => $year->id],
                ['class_teacher_id' => $admin->id]
            );
        }

        echo "সিডিং সফলভাবে সম্পন্ন হয়েছে!\n";
        echo "লগইন তথ্য:\n";
        echo "  অ্যাডমিন: admin@ris.edu.bd\n";
    }
}
