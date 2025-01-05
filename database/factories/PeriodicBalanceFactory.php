<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PeriodicBalance>
 */
class PeriodicBalanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'item_id' => Item::inRandomOrder()->first()->id,
            'warehouse_id' => Warehouse::inRandomOrder()->first()->id,
            'balance_date' => $this->faker->dateTimeBetween('-2 years', 'now')->format('Y-m-d'),
            'balance' => $this->faker->numberBetween(0, 1000),
        ];
    }
}
