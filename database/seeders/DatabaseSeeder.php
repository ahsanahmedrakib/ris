<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Book;
use App\Models\Bus;
use App\Models\ClassRoom;
use App\Models\Notice;
use App\Models\Staff;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        $admin = User::create([
            'name' => 'প্রশাসক',
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

        // Teachers
        $teachers = [];
        $teacherNames = [
            ['name' => 'মোঃ রফিকুল ইসলাম', 'email' => 'rafiq@ris.edu.bd'],
            ['name' => 'ফাতেমা বেগম', 'email' => 'fatema@ris.edu.bd'],
            ['name' => 'মোঃ কামরুজ্জামান', 'email' => 'kamruzzaman@ris.edu.bd'],
            ['name' => 'নাসরিন আক্তার', 'email' => 'nasrin@ris.edu.bd'],
            ['name' => 'মোঃ আব্দুল হাকিম', 'email' => 'hakim@ris.edu.bd'],
        ];

        foreach ($teacherNames as $t) {
            $user = User::create([
                'name' => $t['name'],
                'email' => $t['email'],
                'password' => Hash::make('password'),
                'role' => 'teacher',
                'phone' => '017'.rand(10000000, 99999999),
                'is_active' => true,
            ]);
            $teachers[] = $user;
        }

        // Classes (school serves Play, Nursery, and Classes 1-5)
        $classNames = ['প্লে', 'নার্সারি', '১ম', '২য়', '৩য়', '৪র্থ', '৫ম'];
        $classes = [];
        foreach ($classNames as $i => $name) {
            $class = ClassRoom::create([
                'name' => $name,
                'section' => 'ক',
                'academic_year_id' => $year->id,
                'class_teacher_id' => $teachers[$i % count($teachers)]->id,
            ]);
            $classes[] = $class;
        }

        // Subjects
        $subjectNames = ['বাংলা', 'ইংরেজি', 'গণিত', 'বিজ্ঞান', 'সমাজবিজ্ঞান', 'ইসলাম শিক্ষা', 'প্রশিক্ষণ'];
        foreach ($classes as $class) {
            foreach ($subjectNames as $sName) {
                Subject::create([
                    'name' => $sName,
                    'code' => strtoupper(substr($sName, 0, 3)).'-'.$class->id,
                    'class_id' => $class->id,
                    'teacher_id' => $teachers[array_rand($teachers)]->id,
                ]);
            }
        }

        // Parents
        $parents = [];
        $parentNames = [
            'মোঃ আব্দুর রহমান',
            'করিম উদ্দিন',
            'মোঃ শাহজাহান আলী',
            'নুরুল হক',
            'মোঃ মাহবুবুর রহমান',
            'আলী আকবর',
            'মোঃ ফজলুর রহমান',
            'হাসান মাহমুদ',
            'মোঃ আনোয়ার হোসেন',
            'রশীদ আহমেদ',
            'মোঃ জাহাঙ্গীর আলম',
            'শামসুল হক',
        ];
        foreach ($parentNames as $pName) {
            $user = User::create([
                'name' => $pName,
                'email' => strtolower(str_replace([' ', 'মোঃ'], ['', ''], $pName)).'@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'parent',
                'phone' => '017'.rand(10000000, 99999999),
                'is_active' => true,
            ]);
            $parents[] = $user;
        }

        // Students
        $studentNames = [
            'মোঃ রাকিব হাসান',
            'আয়েশা সিদ্দিকা',
            'মোঃ তানভীর আহমেদ',
            'ফাতিমা খানম',
            'মোঃ সামী উল্লাহ',
            'রুমানা পারভিন',
            'মোঃ আরিফুল ইসলাম',
            'নুসরাত জাহান',
            'মোঃ মেহেদী হাসান',
            'তাসনিম আহমেদ',
            'মোঃ সাকিব আলম',
            'ইসরাত জাহান',
            'মোঃ রাশেদুল ইসলাম',
            'আমিনা বেগম',
            'মোঃ সোহেল রানা',
            'শারমিন সুলতানা',
            'মোঃ ইমরান হোসেন',
            'তানিয়া আক্তার',
            'মোঃ জাহিদুল হক',
            'নাফিসা ইসলাম',
            'মোঃ বিপুল কুমার',
            'মাহমুদা খানম',
            'মোঃ শিহাব উদ্দিন',
            'সাবরিনা রহমান',
            'মোঃ আদনান হোসেন',
            'তানজিলা আক্তার',
            'মোঃ সাইফুল ইসলাম',
            'রুকাইয়া খানম',
            'মোঃ মুশফিকুর রহমান',
            'তাসনিয়া হক',
        ];

        $bloodGroups = ['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'];
        $genders = ['male', 'female'];

        for ($i = 0; $i < count($studentNames); $i++) {
            $parentUser = $parents[$i % count($parents)];
            $class = $classes[$i % 7]; // distribute across Play, Nursery, Class 1-5
            $gender = $i % 2 === 0 ? 'male' : 'female';

            $studentUser = User::create([
                'name' => $studentNames[$i],
                'email' => 'student'.($i + 1).'@ris.edu.bd',
                'password' => Hash::make('password'),
                'role' => 'student',
                'phone' => '017'.rand(10000000, 99999999),
                'is_active' => true,
            ]);

            $student = Student::create([
                'user_id' => $studentUser->id,
                'admission_no' => 'RIS-'.str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'class_id' => $class->id,
                'section' => $i % 3 === 0 ? 'ক' : ($i % 3 === 1 ? 'খ' : 'গ'),
                'roll_no' => ($i % 30) + 1,
                'date_of_birth' => '201'.rand(2, 8).'-'.str_pad(rand(1, 12), 2, '0', STR_PAD_LEFT).'-'.str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT),
                'gender' => $gender,
                'blood_group' => $bloodGroups[array_rand($bloodGroups)],
                'address' => 'নিচুপাড়া, গোপালগঞ্জ',
                'guardian_name' => $parentUser->name,
                'guardian_phone' => $parentUser->phone,
                'guardian_email' => $parentUser->email,
                'is_active' => true,
            ]);

            // Link student to parent
            $student->parents()->attach($parentUser->id, ['relation' => 'পিতা']);
        }

        // Books
        $books = [
            ['title' => 'বাংলা সাহিত্য', 'author' => 'মীর আব্দুস শুকুর আলী', 'category' => 'সাহিত্য', 'isbn' => '978-984-00-1234-5'],
            ['title' => 'গণিত পাঠশালা', 'author' => 'প্রফেসর মোঃ আব্দুল মান্নান', 'category' => 'গণিত', 'isbn' => '978-984-00-1235-2'],
            ['title' => 'বিজ্ঞান আবিষ্কার', 'author' => 'ড. মোঃ শাহ আলম', 'category' => 'বিজ্ঞান', 'isbn' => '978-984-00-1236-9'],
            ['title' => 'ইসলামিক স্টাডিজ', 'author' => 'মাওলানা মোঃ আব্দুল হাকিম', 'category' => 'ধর্মীয়', 'isbn' => '978-984-00-1237-6'],
            ['title' => 'ইংরেজি গ্রামার', 'author' => 'মোঃ মাহবুবুর রহমান', 'category' => 'ভাষা', 'isbn' => '978-984-00-1238-3'],
            ['title' => 'বাংলাদেশ ও বিশ্বপরিচয়', 'author' => 'ড. কামরুজ্জামান', 'category' => 'সমাজবিজ্ঞান', 'isbn' => '978-984-00-1239-0'],
            ['title' => 'কম্পিউটার বিজ্ঞান', 'author' => 'মোঃ রফিকুল ইসলাম', 'category' => 'প্রযুক্তি', 'isbn' => '978-984-00-1240-6'],
            ['title' => 'সুন্দর লেখা', 'author' => 'নাসরিন আক্তার', 'category' => 'ভাষা', 'isbn' => '978-984-00-1241-3'],
        ];

        foreach ($books as $book) {
            Book::create(array_merge($book, [
                'total_copies' => rand(3, 10),
                'available_copies' => rand(1, 5),
                'location' => 'শেল্ফ-'.rand(1, 5),
            ]));
        }

        // Bus
        $bus = Bus::create([
            'bus_no' => 'গোপালগঞ্জ-০১',
            'driver_name' => 'মোঃ আব্দুল কাদের',
            'driver_phone' => '01712345678',
            'capacity' => 40,
            'route_name' => 'গোপালগঞ্জ সদর রুট',
        ]);

        Bus::create([
            'bus_no' => 'গোপালগঞ্জ-০২',
            'driver_name' => 'মোঃ শফিকুল ইসলাম',
            'driver_phone' => '01787654321',
            'capacity' => 35,
            'route_name' => 'কাটাখাল রুট',
        ]);

        // Staff
        foreach ($teachers as $teacher) {
            Staff::create([
                'user_id' => $teacher->id,
                'employee_id' => 'RIS-T'.str_pad($teacher->id, 3, '0', STR_PAD_LEFT),
                'designation' => 'শিক্ষক',
                'department' => 'একাডেমিক',
                'joining_date' => '2020-01-01',
                'salary' => rand(25000, 45000),
                'qualification' => 'এমএ/বিএড',
            ]);
        }

        // Notices
        Notice::create([
            'title' => '২০২৫-২০২৬ শিক্ষাবর্ষে ভর্তি চলছে',
            'content' => 'রেশমা ইন্টারন্যাশনাল স্কুলে ২০২৫-২০২৬ শিক্ষাবর্ষের ভর্তি চলছে। সকল শ্রেণীতে সীমিত আসন রয়েছে। আগ্রহী অভিভাবকরা যত তাড়াতাড়ি সম্ভব ভর্তির জন্য আবেদন করুন।',
            'type' => 'notice',
            'target_role' => 'all',
            'published_by' => $admin->id,
            'published_at' => now(),
            'is_active' => true,
        ]);

        Notice::create([
            'title' => 'ঈদুল ফিতরের ছুটি',
            'content' => 'ঈদুল ফিতরের ছুটি ১ এপ্রিল থেকে ৭ এপ্রিল পর্যন্ত হবে। ৮ এপ্রিল থেকে স্কুল পুনরায় চালু হবে।',
            'type' => 'holiday',
            'target_role' => 'all',
            'published_by' => $admin->id,
            'published_at' => now(),
            'is_active' => true,
        ]);

        Notice::create([
            'title' => 'বার্ষিক ক্রীড়া প্রতিযোগিতা',
            'content' => 'আমাদের স্কুলের বার্ষিক ক্রীড়া প্রতিযোগিতা আগামী ১৫ মার্চ অনুষ্ঠিত হবে। সকল ছাত্রছাত্রীদের অংশগ্রহণ করার জন্য অনুরোধ করা হলো।',
            'type' => 'event',
            'target_role' => 'all',
            'published_by' => $admin->id,
            'published_at' => now(),
            'is_active' => true,
        ]);

        echo "সিডিং সফলভাবে সম্পন্ন হয়েছে!\n";
        echo "লগইন তথ্য:\n";
        echo "  প্রশাসক: admin@ris.edu.bd / password\n";
        echo "  শিক্ষক: rafiq@ris.edu.bd / password\n";
        echo "  অভিভাবক: যেকোনো অভিভাবকের ইমেইল / password\n";
    }
}
