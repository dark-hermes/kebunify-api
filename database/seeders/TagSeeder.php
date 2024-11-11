<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = ['Buah', 'Sayur', 'Penyakit', 'Obat', 'Hama', 'Pupuk', 'Pengairan', 'Pemanenan', 'Pengolahan', 'Pemasaran'];

        foreach ($tags as $tag) {
            \App\Models\Tag::create([
                'name' => $tag,
            ]);
        }
    }
}
