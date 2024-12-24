<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\Transaction;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TransactionItem>
 */
class TransactionItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'transaction_id' => Transaction::inRandomOrder()->first()->id,
            'item_id' =>Item::inRandomOrder()->first()->id ,
            'CTN' => $this->faker->numberBetween(1000, 9999),
            'quantity' => $this->faker->numberBetween(10, 1000),
        ];
    }
}
