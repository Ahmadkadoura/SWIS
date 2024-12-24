<?php

namespace Database\Factories;

use App\Models\Ctn;
use App\Models\Item;
use App\Models\WarehouseItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ctn>
 */
class CtnFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Ctn::class;

    public function definition()
    {
        return [
            'warehouse_item_id' => WarehouseItem::inRandomOrder()->first()->id,
            'item_id' => Item::inRandomOrder()->first()->id,
            'quantity' => $this->faker->numberBetween(1, 100),
            'CTN' => $this->faker->uuid,
        ];
    }
}
