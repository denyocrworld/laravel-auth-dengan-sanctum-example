<?php
namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\FacadesDB;
use Illuminate\Support\FacadesHash;
use App\Models\Supplier;

// php artisan db:seed --class=SuppliersTableSeeder

class SuppliersTableSeeder extends Seeder
{
    public function run(): void
    {
        Supplier::factory(10)->create();
    }
}

