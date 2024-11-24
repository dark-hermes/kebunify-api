<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ForumCommentSeeder extends Seeder
{
    public function run()
    {
        DB::table('forum_comments')->insert([
            // Comments for Forum 1
            [
                'forum_id' => 1,
                'user_id' => 2,
                'content' => 'Saya sarankan gunakan varietas padi unggulan.',
                'parent_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'forum_id' => 1,
                'user_id' => 3,
                'content' => 'Pastikan juga menggunakan pupuk yang cocok.',
                'parent_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'forum_id' => 1,
                'user_id' => 4,
                'content' => 'Jangan lupa perhatikan pola tanam untuk hasil maksimal.',
                'parent_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Comments for Forum 2
            [
                'forum_id' => 2,
                'user_id' => 1,
                'content' => 'Untuk mengatasi hama, gunakan pestisida alami.',
                'parent_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'forum_id' => 2,
                'user_id' => 5,
                'content' => 'Saya berhasil menggunakan campuran minyak neem dan air.',
                'parent_id' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Comments for Forum 3
            [
                'forum_id' => 3,
                'user_id' => 6,
                'content' => 'Pupuk cair organik adalah pilihan terbaik untuk hidroponik.',
                'parent_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'forum_id' => 3,
                'user_id' => 7,
                'content' => 'Apakah ada rekomendasi merek tertentu?',
                'parent_id' => 6,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'forum_id' => 3,
                'user_id' => 8,
                'content' => 'Saya biasa menggunakan pupuk AB mix, hasilnya bagus.',
                'parent_id' => 7,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Comments for Forum 4
            [
                'forum_id' => 4,
                'user_id' => 9,
                'content' => 'Menanam sayuran seperti bayam cocok untuk kebun kecil.',
                'parent_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'forum_id' => 4,
                'user_id' => 10,
                'content' => 'Jangan lupa menggunakan tanah yang gembur.',
                'parent_id' => 9,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
