<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Driver>
 */
class DriverFactory extends Factory
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
                'en' => fake()->name(),
                'ar' => $fakerArabic->name(),
            ],
            'vehicle_number'=>fake()->text(20),
            'national_id'=>fake()->unique()->text(20),
            'transportation_company_name' => [
                'en' => fake()->company(),
                'ar' => $fakerArabic->company(),
            ],
            'phone'=>fake()->unique()->text(20),
        ];
    }
}