<?php

namespace Frooxi\Product\Database\Factories;

use Carbon\Carbon;
use Frooxi\Product\Models\ProductDownloadableLink;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductDownloadableLinkFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ProductDownloadableLink::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $now = Carbon::now()->format('Y-m-d H:i:s');

        $filename = 'ProductImageExampleForUpload.jpg';

        return [
            'url' => '',
            'file' => '/tests/_data/'.$filename,
            'file_name' => $filename,
            'type' => 'file',
            'price' => 0.0000,
            'downloads' => $this->faker->randomNumber(1),
            'created_at' => $now,
            'updated_at' => $now,
        ];
    }
}
