<?php

namespace Database\Seeders;
use Database\Seeders\UsersTableSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UsersTableSeeder::class,
            ProductsTableSeeder::class,
            //seeders @dont-delete-this-lines
        ]);
    }
}
