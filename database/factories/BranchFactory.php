<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Branch>
 */
class BranchFactory extends Factory
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
            'name' => [
                'en' => fake()->company(),
                'ar' => $fakerArabic->company(),
            ],
            'code' => $this->faker->unique()->word,
            'parent_id' =>  $this->faker->numberBetween(0, 10),
            'phone' => $this->faker->unique()->phoneNumber,
            'address' => [
                'en' => fake()->address(),
                'ar' => $fakerArabic->address(),
            ],
        ];
    }
}
