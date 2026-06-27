<?php

namespace Frooxi\Customer\Database\Factories;

use Frooxi\Customer\Models\Wishlist;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerWishlistFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Wishlist::class;

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
