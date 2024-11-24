<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
            ExpertSpecializationSeeder::class,
            ExpertSeeder::class,
            DocumentSeeder::class,
            TagSeeder::class,
            ArticleSeeder::class,
            CategorySeeder::class,
            SellerSeeder::class,
            ProductSeeder::class,
            TransactionSeeder::class,
            ReviewSeeder::class,
            ForumSeeder::class,
            ForumCommentSeeder::class,
            ForumTagSeeder::class,
            CartSeeder::class,
        ]);
    }
}
