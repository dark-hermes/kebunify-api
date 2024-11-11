<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Str;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Transaction::class;

    public function definition()
    {
        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'transaction_number' => 'TRX-' . strtoupper(Str::random(10)),
            'total_amount' => $this->faker->randomFloat(2, 20, 500),
            'status' => $this->faker->randomElement(['pending', 'completed', 'cancelled']),
            'payment_status' => $this->faker->randomElement(['unpaid', 'paid', 'failed']),
            'notes' => $this->faker->sentence,
        ];
    }
}
