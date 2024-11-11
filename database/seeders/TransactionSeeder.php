<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\Product;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        Transaction::factory()->count(20)->create()->each(function ($transaction) {
            $products = Product::inRandomOrder()->take(rand(1, 5))->get();
            foreach ($products as $product) {
                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $product->id,
                    'quantity' => rand(1, 3),
                    'price' => $product->price,
                    'subtotal' => $product->price * rand(1, 3),
                ]);
            }
        });
    }
}
