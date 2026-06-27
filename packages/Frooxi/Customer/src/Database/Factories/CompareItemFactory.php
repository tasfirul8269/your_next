<?php

namespace Frooxi\Customer\Database\Factories;

use Frooxi\Customer\Models\CompareItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompareItemFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CompareItem::class;

    /**
     * Define the model's default state.
     *
     * @throws \Exception
     */
    public function definition(): array
    {
        return [];
    }
}
