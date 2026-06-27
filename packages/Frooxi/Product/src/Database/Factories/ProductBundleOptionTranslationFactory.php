<?php

namespace Frooxi\Product\Database\Factories;

use Frooxi\Product\Models\ProductBundleOptionTranslation;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductBundleOptionTranslationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ProductBundleOptionTranslation::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'label' => $this->faker->words(3, true),
            'locale' => app()->getLocale(),
        ];
    }
}
