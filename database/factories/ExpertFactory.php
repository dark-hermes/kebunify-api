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
            // random item in array
            'specialization' => $this->faker->randomElement([
                'Agronomi',
                'Hortikultura',
                'Fitopatologi',
                'Entomologi',
                'Agroekologi',
                'Agroteknologi',
                'Pemuliaan Tanaman',
                'Perkebunan Berkelanjutan',
                'Manajemen Perkebunan',
                'Klimatologi Pertanian']),
            'consultation_price' => $this->faker->randomFloat(2, 30000, 100000),
        ];
    }
}
