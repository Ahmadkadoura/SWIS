<?php

namespace Database\Factories;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use MatanYadaev\EloquentSpatial\Objects\Point;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Warehouse>
 */
class WarehouseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $fakerArabic = \Faker\Factory::create('ar_SA');




        return [
//            'name' => $this->faker->company,
            'name' => [
                'en' => fake()->company(),
                'ar' => $fakerArabic->company(),
            ],
            'code' => $this->faker->unique()->word,
            'branch_id' => Branch::inRandomOrder()->first()->id,
            'capacity' => $this->faker->numberBetween(100, 1000),
            'parent_id' => $this->faker->numberBetween(0, 10),
            'user_id' => User::where('id', '!=', 1)->where('id', '!=', 3)->inRandomOrder()->first()->id,
            'location' => new Point($this->faker->longitude, $this->faker->latitude),
            'is_Distribution_point' => $this->faker->boolean(),
        ];
    }
}
