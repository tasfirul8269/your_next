<?php

namespace Frooxi\User\Database\Factories;

use Frooxi\User\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Role::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => preg_replace('/[^a-zA-Z ]/', '', $this->faker->name()),
            'permission_type' => $this->faker->randomElement(['custom', 'all']),
        ];
    }
}
