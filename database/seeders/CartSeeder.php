<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;

class CartSeeder extends Seeder
{
    public function run()
    {
        // Get all users
        $users = User::all();

        // Loop through each user
        foreach ($users as $user) {
            // Create a cart for the user
            $cart = Cart::create([
                'user_id' => $user->id,
            ]);

            // Get random products
            $products = Product::inRandomOrder()->take(5)->get();

            // Add products to the cart
            foreach ($products as $product) {
                CartItem::create([
                    'cart_id' => $cart->id,
                    'product_id' => $product->id,
                    'quantity' => rand(1, 5), // Random quantity between 1 and 5
                ]);
            }
        }
    }
}