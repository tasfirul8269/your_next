<?php

namespace Frooxi\Product\Repositories;

use Frooxi\Core\Eloquent\Repository;
use Illuminate\Support\Collection;

class ProductReviewRepository extends Repository
{
    /**
     * Specify Model class name
     */
    public function model(): string
    {
        return 'Frooxi\Product\Contracts\ProductReview';
    }

    /**
     * Retrieve review for customerId
     *
     * @return Collection
     */
    public function getCustomerReview()
    {
        $reviews = $this->model
            ->where(['customer_id' => auth()->guard('customer')->user()->id])
            ->with('product')
            ->paginate(5);

        return $reviews;
    }
}
