<?php

namespace Database\Seeders;

use App\Models\Expert;
use App\Models\Article;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Article::factory(5)->create([
        //     'expert_id' => Expert::inRandomOrder()->first()->id,
        //     'is_published' => true,
        //     'is_premium' => false,
        // ])->each(function ($article) {
        //     $article->tags()->attach([1, 2, 3]);
        // });

        // Article::factory(5)->create([
        //     'expert_id' => Expert::inRandomOrder()->first()->id,
        //     'is_published' => true,
        //     'is_premium' => true,
        // ])->each(function ($article) {
        //     $article->tags()->attach([4, 5, 6]);
        // });

        // Article::factory(5)->create([
        //     'expert_id' => Expert::inRandomOrder()->first()->id,
        //     'is_published' => false,
        //     'is_premium' => false,
        // ])->each(function ($article) {
        //     $article->tags()->attach([7, 8, 9]);
        // });

        $json = file_get_contents(database_path('seeders/articles.json'));
        $articles = json_decode($json, true);

        foreach ($articles as $article) {
            $newArticle = Article::create([
                'title' => $article['title'],
                'content' => $article['content'],
                'expert_id' => Expert::inRandomOrder()->first()->id,
                'is_published' => true,
                'is_premium' => false,
                "image" => $article['image'],
            ]);

            foreach ($article['tags'] as $tag) {
                $tag = \App\Models\Tag::firstOrCreate(['name' => $tag]);
                $newArticle->tags()->attach($tag->id);
            }
        }
    }
}
