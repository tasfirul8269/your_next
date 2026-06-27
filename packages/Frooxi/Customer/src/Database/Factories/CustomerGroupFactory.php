<?php

namespace Frooxi\Customer\Database\Factories;

use Frooxi\Customer\Models\CustomerGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerGroupFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CustomerGroup::class;

    /**
     * Define the model's default state.
     *
     * @throws \Exception
     */
    public function definition(): array
    {
        return [
            'name' => ucfirst($this->faker->word),
            'is_user_defined' => $this->faker->boolean,
            'code' => $this->faker->regexify('/^[a-zA-Z]+[a-zA-Z0-9_]+$/'),
        ];
    }
}
