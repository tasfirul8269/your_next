<?php

namespace Frooxi\Product\Database\Factories;

use Frooxi\Product\Models\ProductCustomerGroupPrice;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductCustomerGroupPriceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ProductCustomerGroupPrice::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [];
    }
}
