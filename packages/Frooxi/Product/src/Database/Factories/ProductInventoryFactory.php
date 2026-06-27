<?php

namespace Frooxi\Product\Database\Factories;

use Frooxi\Product\Models\ProductInventory;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductInventoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ProductInventory::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'qty' => $this->faker->numberBetween(100, 200),
        ];
    }
}
