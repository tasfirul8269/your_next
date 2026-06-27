<?php

namespace Frooxi\Product\Database\Factories;

use Frooxi\Product\Models\ProductAttributeValue;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductAttributeValueFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ProductAttributeValue::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [];
    }
}
