<?php

namespace Frooxi\Product\Database\Factories;

use Frooxi\Product\Models\ProductGroupedProduct;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductGroupedProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ProductGroupedProduct::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'qty' => rand(10, 50),
            'sort_order' => 0,
        ];
    }
}
