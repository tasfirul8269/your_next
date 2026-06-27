<?php

namespace Frooxi\Core\Database\Factories;

use Frooxi\Core\Models\ChannelTranslation;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChannelTranslationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ChannelTranslation::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'locale' => 'en',
            'name' => $this->faker->word,
            'home_seo' => [
                'meta_title' => $this->faker->sentence(),
                'meta_description' => $this->faker->paragraph(),
                'meta_keywords' => $this->faker->words(5, true),
            ],
        ];
    }
}
