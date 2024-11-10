<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Expert>
 */
class ExpertFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'start_year' => $this->faker->numberBetween(2010, 2021),
            'consulting_fee' => $this->faker->numberBetween(30000, 100000),
            'discount' => $this->faker->numberBetween(0, 50),
            'bio' => $this->faker->paragraph,
        ];
    }
}
