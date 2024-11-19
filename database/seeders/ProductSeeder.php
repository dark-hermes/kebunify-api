<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;


class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        Product::factory()->count(50)->create()->each(function ($product) {
            $product->total_sales = rand(0, 100); // Random total sales for demonstration
            $product->save();
        });
    }
}
