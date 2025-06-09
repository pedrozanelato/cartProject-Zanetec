<?php

namespace Modules\Order\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Order\Entities\Order;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'total_value' => $this->faker->randomFloat(2, 100, 2000),
            'payment_method' => $this->faker->randomElement(['credit_card', 'boleto', 'pix']),
            'times' => $this->faker->numberBetween(1, 12),
            'payment_data' => [
                'transaction_id' => $this->faker->uuid,
                'status' => $this->faker->randomElement(['paid', 'pending', 'failed']),
                'paid_at' => now()->subDays(rand(0, 10))->toDateTimeString(),
            ],
        ];
    }
}
