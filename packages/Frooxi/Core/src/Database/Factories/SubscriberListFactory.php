<?php

namespace Frooxi\Core\Database\Factories;

use Frooxi\Core\Models\SubscribersList;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubscriberListFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SubscribersList::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'email' => $this->faker->safeEmail(),
            'channel_id' => core()->getCurrentChannel()->id,
            'is_subscribed' => 1,
            'token' => uniqid(),
        ];
    }
}
