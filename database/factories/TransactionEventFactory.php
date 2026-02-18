<?php

namespace Database\Factories;

use App\Models\TransactionEvent;
use App\Models\Event;
use App\Models\Person;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TransactionEvent>
 */
class TransactionEventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'event_id' => Event::factory(),
            'transaction_type' => fake()->randomElement(TransactionEvent::TRANSACTION_TYPES),
            'price' => fake()->randomFloat(2, 10, 500),
            'buyer_id' => Person::factory(),
            'seller_id' => Person::factory(),
        ];
    }
}
