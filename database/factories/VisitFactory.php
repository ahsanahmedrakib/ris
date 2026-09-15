<?php

namespace Database\Factories;

use App\Models\Visit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Visit>
 */
class VisitFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $ip = $this->faker->ipv4();

        return [
            'visitor_id' => $ip,
            'ip_address' => $ip,
            'user_agent' => $this->faker->userAgent(),
            'url' => 'https://example.com/page/'.$this->faker->numberBetween(1, 1000),
            'visited_at' => $this->faker->dateTimeBetween('-1 week'),
        ];
    }
}
