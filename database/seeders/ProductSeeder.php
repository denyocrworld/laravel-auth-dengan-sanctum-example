<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::create([
            'product_name' => 'TEH GELAS OT',
            'price' => 100,
            'description' => '-',
        ]);

        Product::create([
            'product_name' => 'KP LIONG GULA',
            'price' => 33,
            'description' => '-',
        ]);
    }
}
