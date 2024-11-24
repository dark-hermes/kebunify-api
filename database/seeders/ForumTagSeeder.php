<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ForumTagSeeder extends Seeder
{
    public function run()
    {
        DB::table('forum_tag')->insert([
            // Tags for Forum 1
            ['forum_id' => 1, 'tag_id' => 2], // Tag: Sayur
            ['forum_id' => 1, 'tag_id' => 6], // Tag: Pupuk

            // Tags for Forum 2
            ['forum_id' => 2, 'tag_id' => 3], // Tag: Penyakit
            ['forum_id' => 2, 'tag_id' => 5], // Tag: Hama

            // Tags for Forum 3
            ['forum_id' => 3, 'tag_id' => 6], // Tag: Pupuk
            ['forum_id' => 3, 'tag_id' => 7], // Tag: Pengairan

            // Tags for Forum 4
            ['forum_id' => 4, 'tag_id' => 1], // Tag: Buah
            ['forum_id' => 4, 'tag_id' => 8], // Tag: Pemanenan

            // Tags for Forum 5
            ['forum_id' => 5, 'tag_id' => 9], // Tag: Pengolahan
            ['forum_id' => 5, 'tag_id' => 10], // Tag: Pemasaran

            // Additional Forum-Tag Relationships
            ['forum_id' => 6, 'tag_id' => 2], // Tag: Sayur
            ['forum_id' => 6, 'tag_id' => 4], // Tag: Obat

            ['forum_id' => 7, 'tag_id' => 3], // Tag: Penyakit
            ['forum_id' => 7, 'tag_id' => 5], // Tag: Hama

            ['forum_id' => 8, 'tag_id' => 7], // Tag: Pengairan
            ['forum_id' => 8, 'tag_id' => 8], // Tag: Pemanenan
        ]);
    }
}
