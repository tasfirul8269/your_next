<?php

namespace Frooxi\Sales\Database\Factories;

use Frooxi\Sales\Models\Refund;
use Illuminate\Database\Eloquent\Factories\Factory;

class RefundFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Refund::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [];
    }
}
