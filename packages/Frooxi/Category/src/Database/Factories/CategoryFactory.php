<?php

namespace Frooxi\Category\Database\Factories;

use Frooxi\Category\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Category::class;

    /**
     * @var string[]
     */
    protected $states = [
        'inactive',
        'rtl',
    ];

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'status' => 1,
            'position' => $this->faker->randomDigit(),
            'parent_id' => 1,
        ];
    }

    public function inactive(): CategoryFactory
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 0,
            ];
        });
    }

    /**
     * Handle rtl state
     */
    public function rtl(): CategoryFactory
    {
        return $this->state(function (array $attributes) {
            return [
                'direction' => 'rtl',
            ];
        });
    }
}
