<?php

namespace Database\Factories;

use App\Models\CampusNews;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CampusNews>
 */
class CampusNewsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(5),
            'date' => fake()->dateTimeThisYear(),
            'description' => fake()->paragraph(),
            'is_active' => true,
            'sort_order' => fake()->numberBetween(0, 10),
        ];
    }
}
