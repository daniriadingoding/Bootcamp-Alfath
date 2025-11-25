<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FoodMenu;
use App\Models\User;

class FoodMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Menu untuk Ayam Geprek Bojong
        $merchantGeprek = User::where('email', 'geprek@contoh.com')->first();
        if ($merchantGeprek) {
            FoodMenu::create([
                'user_id' => $merchantGeprek->id,
                'name' => 'Paket Geprek Bensu KW',
                'description' => 'Nasi + Ayam Geprek + Lalapan + Es Teh',
                'price' => 15000.00,
                'image' => null, 
            ]);
            FoodMenu::create([
                'user_id' => $merchantGeprek->id,
                'name' => 'Ayam Geprek Mozarella',
                'description' => 'Ayam geprek dengan toping keju mozarella lumer',
                'price' => 20000.00,
            ]);
            FoodMenu::create([
                'user_id' => $merchantGeprek->id,
                'name' => 'Jamur Crispy',
                'description' => 'Jamur tiram goreng tepung renyah',
                'price' => 8000.00,
            ]);
        }

        // 2. Menu untuk Nasi Goreng Mas Yono
        $merchantNasgor = User::where('email', 'nasgor@contoh.com')->first();
        if ($merchantNasgor) {
            FoodMenu::create([
                'user_id' => $merchantNasgor->id,
                'name' => 'Nasi Goreng Spesial',
                'description' => 'Nasi goreng dengan telur, ayam, ati ampela, dan bakso',
                'price' => 25000.00,
            ]);
            FoodMenu::create([
                'user_id' => $merchantNasgor->id,
                'name' => 'Kwetiau Goreng Sapi',
                'description' => 'Kwetiau goreng dengan potongan daging sapi',
                'price' => 28000.00,
            ]);
            FoodMenu::create([
                'user_id' => $merchantNasgor->id,
                'name' => 'Capcay Kuah',
                'description' => 'Sayuran segar dengan kuah kental gurih',
                'price' => 22000.00,
            ]);
        }

        // 3. Menu untuk Sate Padang Pagi Sore
        $merchantSate = User::where('email', 'sate@contoh.com')->first();
        if ($merchantSate) {
            FoodMenu::create([
                'user_id' => $merchantSate->id,
                'name' => 'Sate Padang (10 Tusuk)',
                'description' => 'Sate daging sapi dengan bumbu kuah padang kental',
                'price' => 30000.00,
            ]);
            FoodMenu::create([
                'user_id' => $merchantSate->id,
                'name' => 'Ketupat Sayur Padang',
                'description' => 'Ketupat dengan gulai nangka dan pakis',
                'price' => 15000.00,
            ]);
            FoodMenu::create([
                'user_id' => $merchantSate->id,
                'name' => 'Keripik Balado',
                'description' => 'Keripik singkong pedas manis khas Padang',
                'price' => 10000.00,
            ]);
        }

        $merchantBaru = User::where('email', 'new@contoh.com')->first();
        if ($merchantBaru) {
            FoodMenu::create([
                'user_id' => $merchantBaru->id,
                'name' => 'Promo Opening: Mie Ayam',
                'description' => 'Mie ayam jamur spesial promo pembukaan',
                'price' => 12000.00,
            ]);
        }
    }
}