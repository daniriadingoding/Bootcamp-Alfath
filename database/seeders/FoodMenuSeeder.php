<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FoodMenu;

class FoodMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        FoodMenu::create([
            'name' => 'Nasi Goreng Spesial',
            'description' => 'Nasi goreng dengan telur, ayam, ati ampela, dan bakso',
            'price' => 25000.00
        ]);
        FoodMenu::create([
            'name' => 'Es Teh Manis',
            'description' => 'Minuman es teh manisss',
            'price' => 8000.00
        ]);
        FoodMenu::create([
            'name' => 'Kentang Goreng',
            'description' => 'Kentang goreng french fries',
            'price' => 15000.00
        ]);
    }
}