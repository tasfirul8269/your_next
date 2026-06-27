<?php

namespace Frooxi\Sales\Database\Factories;

use Frooxi\Sales\Models\OrderAddress;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderAddressFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = OrderAddress::class;

    /**
     * @var string[]
     */
    protected $states = [
        'shipping',
    ];

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'address_type' => OrderAddress::ADDRESS_TYPE_BILLING,
        ];
    }

    public function shipping(): void
    {
        $this->state(function () {
            return [
                'address_type' => OrderAddress::ADDRESS_TYPE_SHIPPING,
            ];
        });
    }
}
