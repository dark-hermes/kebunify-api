<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ExpertSpecialization;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ExpertSpecializationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $specializations = [
            'Agronomi',
            'Hortikultura',
            'Fitopatologi',
            'Entomologi',
            'Agroekologi',
            'Agroteknologi',
            'Pemuliaan Tanaman',
            'Perkebunan Berkelanjutan',
            'Manajemen Perkebunan',
            'Klimatologi Pertanian',
        ];

        foreach ($specializations as $specialization) {
            ExpertSpecialization::create([
                'name' => $specialization,
            ]);
        }
    }
}
