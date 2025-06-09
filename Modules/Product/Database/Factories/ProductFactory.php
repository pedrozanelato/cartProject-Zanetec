<?php

namespace Modules\Product\Database\Factories;
use Modules\Product\Entities\Product;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->words(2, true),
            'unit_price' => $this->faker->randomFloat(2, 10, 1000),
            'stock' => $this->faker->numberBetween(0, 100),
            'file' => 'products/' . $this->faker->uuid . '.jpg',
        ];
    }
}
