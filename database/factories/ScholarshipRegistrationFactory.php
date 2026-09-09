<?php

namespace Database\Factories;

use App\Models\ScholarshipRegistration;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ScholarshipRegistration>
 */
class ScholarshipRegistrationFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $classNo = fake()->numberBetween(1, 5);

        return [
            'registration_no' => sprintf('RIS-SC-%s-%d%02d', now()->format('y'), $classNo, fake()->numberBetween(1, 99)),
            'student_name' => fake()->name(),
            'father_name' => fake()->name(),
            'mother_name' => fake()->name(),
            'school_name' => fake()->company(),
            'class_no' => $classNo,
            'serial_no' => fake()->numberBetween(1, 99),
            'roll_no' => (string) fake()->numberBetween(1, 50),
            'mobile_no' => '017'.fake()->numerify('########'),
            'bkash_no' => '019'.fake()->numerify('########'),
            'status' => 'pending',
        ];
    }

    public function configure(): static
    {
        return $this->afterMaking(function (ScholarshipRegistration $registration): void {
            if (blank($registration->registration_no)) {
                $registration->registration_no = ScholarshipRegistration::nextRegistrationNo((int) $registration->class_no);
                $registration->serial_no = (int) str($registration->registration_no)->afterLast('-')->substr(1);
            }
        });
    }
}
