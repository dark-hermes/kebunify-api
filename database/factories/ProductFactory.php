<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use App\Models\Seller;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Product::class;

    public function definition()
    {
        // Ensure the user_id is from a user who is a seller
        $seller = Seller::inRandomOrder()->first();

        // If no seller is found, create one
        if (!$seller) {
            $seller = Seller::factory()->create();
        }

        return [
            'name' => $this->faker->word,
            'description' => $this->faker->paragraph,
            'price' => $this->faker->randomFloat(2, 10, 100),
            'category_id' => Category::inRandomOrder()->first()->id,
            'stock' => $this->faker->numberBetween(1, 100),
            'image_url' => $this->faker->imageUrl(),
            'user_id' => $seller->user_id, // Use the user_id from the seller
        ];
    }
}
