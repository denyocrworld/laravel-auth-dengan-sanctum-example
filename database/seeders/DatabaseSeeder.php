<?php

namespace Database\Seeders;

use Database\Seeders\CustomersTableSeeder;
use Database\Seeders\UsersTableSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UsersTableSeeder::class,
            SuppliersTableSeeder::class,
            //seeders @dont-delete-this-lines
        ]);
    }
}
