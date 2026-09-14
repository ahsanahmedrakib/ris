<?php

namespace Database\Factories;

use App\Models\Admission;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Admission>
 */
class AdmissionFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        $banglaNames = ['আব্দুল করিম', 'ফাতেমা খাতুন', 'মোহাম্মদ রাশেদ', 'রোকেয়া বেগম', 'কামরুল হাসান', 'শারমিন আক্তার', 'আনোয়ার হোসেন', 'নাসিমা আক্তার'];
        $occupations = ['কৃষক', 'শিক্ষক', 'ব্যবসায়ী', 'চিকিৎসক', 'সরকারি কর্মচারী', 'প্রবাসী'];

        return [
            'admission_no' => fn () => Admission::nextAdmissionNo(fake()->randomElement(Admission::CLASS_OPTIONS)),
            'status' => 'pending',
            'academic_year' => '২৬',
            'student_name_bn' => fake()->randomElement($banglaNames),
            'student_name_en' => fake()->name(),
            'dob' => fake()->dateTimeBetween('-8 years', '-4 years'),
            'nationality' => 'বাংলাদেশী',
            'religion' => fake()->randomElement(['ইসলাম', 'হিন্দু', 'খ্রিস্টান', 'বৌদ্ধ']),
            'father_name_bn' => fake()->randomElement($banglaNames),
            'father_name_en' => fake()->name(),
            'father_occupation' => fake()->randomElement($occupations),
            'mother_name_bn' => fake()->randomElement($banglaNames),
            'mother_name_en' => fake()->name(),
            'mother_occupation' => fake()->randomElement($occupations),
            'phone' => '017'.fake()->numerify('########'),
            'student_photo' => null,
        ];
    }
}
