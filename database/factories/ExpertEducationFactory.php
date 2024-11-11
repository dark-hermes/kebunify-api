<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ExpertEducation>
 */
class ExpertEducationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $degrees = ['Sarjana Pertanian', 'Magister Pertanian', 'Doktor Pertanian'];
        $institutions = ['IPB University', 'Universitas Gadjah Mada'];
        $field_of_studies = ['Agribisnis', 'Agroteknologi', 'Ilmu Tanah', 'Proteksi Tanaman', 'Pertanian'];
        return [
            'degree' => $degrees[array_rand($degrees)],
            'institution' => $institutions[array_rand($institutions)],
            'graduation_year' => $this->faker->numberBetween(2010, 2021),
            'field_of_study' => $field_of_studies[array_rand($field_of_studies)],
        ];
    }
}
