<?php

namespace Frooxi\Product\Database\Factories;

use Frooxi\Product\Models\ProductBundleOptionProduct;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductBundleOptionProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ProductBundleOptionProduct::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'qty' => 1,
            'is_user_defined' => 1,
            'is_default' => 0,
            'sort_order' => 0,
        ];
    }
}
