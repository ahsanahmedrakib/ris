<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\ClassRoom;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        $admin = User::create([
            'name' => 'অ্যাডমিন',
            'email' => 'admin@ris.edu.bd',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '01700000000',
            'is_active' => true,
        ]);

        // Academic Year
        $year = AcademicYear::create([
            'name' => '২০২৫-২০২৬',
            'start_date' => '2025-01-01',
            'end_date' => '2025-12-31',
            'is_current' => true,
        ]);

        // Classes (school serves Play, Nursery, and Classes 1-5)
        $classNames = ['প্লে', 'নার্সারি', 'কেজি', '১ম', '২য়', '৩য়', '৪র্থ', '৫ম'];
        foreach ($classNames as $name) {
            ClassRoom::create([
                'name' => $name,
                'section' => 'ক',
                'academic_year_id' => $year->id,
                'class_teacher_id' => $admin->id,
            ]);
        }

        echo "সিডিং সফলভাবে সম্পন্ন হয়েছে!\n";
        echo "লগইন তথ্য:\n";
        echo "  অ্যাডমিন: admin@ris.edu.bd / password\n";
    }
}
