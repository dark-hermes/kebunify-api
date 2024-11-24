<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ForumSeeder extends Seeder
{
    public function run()
    {
        DB::table('forums')->insert([
            [
                'title' => 'Cara menanam padi yang efektif',
                'user_id' => 1,
                'likes' => 12,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Bagaimana cara mengatasi hama pada jagung?',
                'user_id' => 2,
                'likes' => 20,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Pupuk terbaik untuk tanaman hidroponik',
                'user_id' => 3,
                'likes' => 15,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Tips mengelola kebun kecil di rumah',
                'user_id' => 4,
                'likes' => 8,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Cara membuat kompos dari limbah dapur',
                'user_id' => 5,
                'likes' => 25,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Jenis tanaman yang cocok untuk pemula',
                'user_id' => 6,
                'likes' => 18,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Panduan memilih bibit unggul untuk pertanian',
                'user_id' => 7,
                'likes' => 30,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Bagaimana cara membuat irigasi sederhana?',
                'user_id' => 8,
                'likes' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Manfaat rotasi tanaman untuk kesuburan tanah',
                'user_id' => 9,
                'likes' => 22,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Teknik bercocok tanam tanpa tanah',
                'user_id' => 10,
                'likes' => 17,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
