<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Expert;
use App\Models\ExpertSpecialization;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ExpertSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $experts = User::where('email', 'like', 'expert%')->get();

        foreach ($experts as $expert) {
            $createdExpert = Expert::factory()->create([
                'user_id' => $expert->id,
                'expert_specialization_id' => ExpertSpecialization::inRandomOrder()->first()->id,
            ]);

            $createdExpert->educations()->createMany([
                [
                    'degree' => 'S.P.',
                    'field_of_study' => 'Pertanian',
                    'institution' => 'IPB University',
                    'graduation_year' => 2010,
                ],
                [
                    'degree' => 'M.P.',
                    'field_of_study' => 'Pertanian',
                    'institution' => 'IPB University',
                    'graduation_year' => 2012,
                ],
            ]);

            $createdExpert->experiences()->create([
                'position' => 'Kepala Laboratorium',
                'company' => 'IPB University',
                'start_year' => 2011,
                'end_year' => 2015,
            ]);
        }
    }
}
