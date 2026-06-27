<?php

namespace Frooxi\Admin\Tests\Concerns;

use Frooxi\User\Contracts\Admin as AdminContract;
use Frooxi\User\Models\Admin as AdminModel;

trait AdminTestBench
{
    /**
     * Login as customer.
     */
    public function loginAsAdmin(?AdminContract $admin = null): AdminContract
    {
        $admin = $admin ?? AdminModel::factory()->create();

        $this->actingAs($admin, 'admin');

        return $admin;
    }
}
