<?php

namespace Frooxi\Installer\Database\Seeders;

use Frooxi\Installer\Database\Seeders\Attribute\DatabaseSeeder as AttributeSeeder;
use Frooxi\Installer\Database\Seeders\Category\DatabaseSeeder as CategorySeeder;
use Frooxi\Installer\Database\Seeders\Core\DatabaseSeeder as CoreSeeder;
use Frooxi\Installer\Database\Seeders\Customer\DatabaseSeeder as CustomerSeeder;
use Frooxi\Installer\Database\Seeders\Inventory\DatabaseSeeder as InventorySeeder;
use Frooxi\Installer\Database\Seeders\Shop\ThemeCustomizationTableSeeder as ShopSeeder;
use Frooxi\Installer\Database\Seeders\User\DatabaseSeeder as UserSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @param  array  $parameters
     * @return void
     */
    public function run($parameters = [])
    {
        $this->call(AttributeSeeder::class, false, ['parameters' => $parameters]);

        $this->call(CategorySeeder::class, false, ['parameters' => $parameters]);

        $this->call(CoreSeeder::class, false, ['parameters' => $parameters]);

        $this->call(CustomerSeeder::class, false, ['parameters' => $parameters]);

        $this->call(InventorySeeder::class, false, ['parameters' => $parameters]);

        $this->call(ShopSeeder::class, false, ['parameters' => $parameters]);

        $this->call(UserSeeder::class, false, ['parameters' => $parameters]);
    }
}
