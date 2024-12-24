<?php

namespace Database\Factories;

use App\Enums\transactionModeType;
use App\Enums\transactionType;
use App\Enums\transactionStatusType;
use App\Http\services\QRCodeService;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

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
    public function definition(): array
    {
        $fakerArabic = \Faker\Factory::create('ar_SA');



        $sourceable = $this->getRandomSourceable();
        $destinationable = $this->getRandomDestinationable();

        return [
            'is_convoy' => $this->faker->boolean(),
            'notes' => [
                'en' => fake()->optional()->sentence,
                'ar' => $fakerArabic->optional()->sentence,
            ],

            'code' => $this->faker->word,
            'status' => $this->faker->randomElement(transactionStatusType::class),
            'date' => $this->faker->date(),
            'transaction_type' => $this->faker->randomElement(transactionType::class),
            'transaction_mode_type' => $this->faker->randomElement(transactionModeType::class),
            'waybill_num' => $this->faker->numberBetween(1000, 9999),
            'waybill_img' => $this->faker->imageUrl(),
            'qr_code' => null,

            // Polymorphic relations
            'sourceable_type' => $sourceable::class,
            'sourceable_id' => $sourceable->id,
            'destinationable_type' => $destinationable::class,
            'destinationable_id' => $destinationable->id,
        ];
    }

    /**
     * Configure the factory.
     */
    public function configure()
    {
        return $this->afterCreating(function (Transaction $transaction) {
            $qrCodeService = app(QRCodeService::class);
            $qrCodePath = $qrCodeService->generateQRCode($transaction);

            $transaction->qr_code = $qrCodePath;
            $transaction->save();
        });
    }

    /**
     * Get a random model instance for sourceable.
     */
    protected function getRandomSourceable()
    {
        return $this->faker->randomElement([
            User::factory()->create(),
            Warehouse::factory()->create(),
        ]);
    }

    /**
     * Get a random model instance for destinationable.
     */
    protected function getRandomDestinationable()
    {
        return $this->faker->randomElement([
            User::factory()->create(),
            Warehouse::factory()->create(),
        ]);
    }
}
