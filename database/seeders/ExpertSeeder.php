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
            Expert::factory()->create([
                'user_id' => $expert->id,
                'expert_specialization_id' => ExpertSpecialization::inRandomOrder()->first()->id,
            ]);
        }
    }
}
