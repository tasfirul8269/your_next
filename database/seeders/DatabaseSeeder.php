<?php

namespace Database\Seeders;

use Frooxi\Installer\Database\Seeders\DatabaseSeeder as FrooxiDatabaseSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(FrooxiDatabaseSeeder::class);

        $this->call(ClothingProductSeeder::class);
    }
}
