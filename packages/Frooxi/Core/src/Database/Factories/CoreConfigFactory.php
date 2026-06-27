<?php

namespace Frooxi\Core\Database\Factories;

use Frooxi\Core\Models\CoreConfig;
use Illuminate\Database\Eloquent\Factories\Factory;

class CoreConfigFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CoreConfig::class;

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
