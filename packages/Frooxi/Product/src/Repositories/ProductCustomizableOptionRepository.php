<?php

namespace Frooxi\Product\Repositories;

use Frooxi\Core\Eloquent\Repository;
use Frooxi\Product\Contracts\Product;
use Frooxi\Product\Contracts\ProductCustomizableOption;
use Illuminate\Container\Container;
use Illuminate\Support\Str;

class ProductCustomizableOptionRepository extends Repository
{
    /**
     * Create a new repository instance.
     *
     * @return void
     */
    public function __construct(
        protected ProductCustomizableOptionPriceRepository $productCustomizableOptionPriceRepository,
        Container $container
    ) {
        parent::__construct($container);
    }

    /**
     * Specify model class name.
     */
    public function model(): string
    {
        return ProductCustomizableOption::class;
    }

    /**
     * Save customizable options.
     *
     * @param  array  $data
     * @param  Product  $product
     * @return void
     */
    public function saveCustomizableOptions($data, $product)
    {
        $previousCustomizableOptionIds = $product->customizable_options()->pluck('id');

        if (isset($data['customizable_options'])) {
            foreach ($data['customizable_options'] as $customizableOptionId => $customizableOptionInputs) {
                if (Str::contains($customizableOptionId, 'option_')) {
                    $productCustomizableOption = $this->create(array_merge([
                        'product_id' => $product->id,
                    ], $customizableOptionInputs));
                } else {
                    $productCustomizableOption = $this->find($customizableOptionId);

                    if (is_numeric($index = $previousCustomizableOptionIds->search($customizableOptionId))) {
                        $previousCustomizableOptionIds->forget($index);
                    }

                    $this->update($customizableOptionInputs, $customizableOptionId);
                }

                $this->productCustomizableOptionPriceRepository->saveCustomizableOptionPrices($customizableOptionInputs, $productCustomizableOption);
            }
        }

        foreach ($previousCustomizableOptionIds as $previousCustomizableOptionId) {
            $this->delete($previousCustomizableOptionId);
        }
    }
}
