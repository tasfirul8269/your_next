<?php

namespace Frooxi\Admin\Tests;

use Frooxi\Admin\Tests\Concerns\AdminTestBench;
use Frooxi\Core\Tests\Concerns\CoreAssertions;
use Tests\TestCase;

class AdminTestCase extends TestCase
{
    use AdminTestBench, CoreAssertions;
}
