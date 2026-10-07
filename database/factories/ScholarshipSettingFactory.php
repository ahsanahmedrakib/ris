<?php

namespace Database\Factories;

use App\Models\ScholarshipSetting;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ScholarshipSetting>
 */
class ScholarshipSettingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'is_open' => true,
            'open_from' => null,
            'open_to' => null,
        ];
    }

    /**
     * Turn the public registration window off entirely.
     */
    public function closed(): static
    {
        return $this->state(fn () => ['is_open' => false]);
    }
}
