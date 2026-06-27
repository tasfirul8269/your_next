<?php

namespace Frooxi\Shop\Tests\Concerns;

use Frooxi\Customer\Contracts\Customer as CustomerContract;
use Frooxi\Faker\Helpers\Customer as CustomerFaker;

trait ShopTestBench
{
    /**
     * Login as customer.
     */
    public function loginAsCustomer(?CustomerContract $customer = null): CustomerContract
    {
        $customer = $customer ?? (new CustomerFaker)->factory()->create();

        $this->actingAs($customer);

        return $customer;
    }
}
