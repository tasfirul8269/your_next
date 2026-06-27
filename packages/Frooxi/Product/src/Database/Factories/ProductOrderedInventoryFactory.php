<?php

namespace Frooxi\Product\Database\Factories;

use Frooxi\Product\Models\ProductOrderedInventory;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductOrderedInventoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ProductOrderedInventory::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'qty' => $this->faker->numberBetween(100, 200),
            'channel_id' => 1,
        ];
    }
}
