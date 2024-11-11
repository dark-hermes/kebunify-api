<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Seller;
use App\Models\User;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Seller>
 */
class SellerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Seller::class;

    public function definition()
    {
        return [
            'store_name' => $this->faker->company,
            'address' => $this->faker->address,
            'avatar' => $this->faker->imageUrl(),
            'status' => $this->faker->randomElement(['active', 'inactive']),
            'user_id' => User::inRandomOrder()->first()->id,
        ];
    }
}
