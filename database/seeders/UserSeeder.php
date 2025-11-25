<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Buat Akun Admin
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@contoh.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // 2. Buat Akun Customer
        User::create([
            'name' => 'Customer User',
            'email' => 'customer@contoh.com',
            'password' => Hash::make('password'),
            'role' => 'customer',
        ]);

        // 3. Buat Beberapa Merchant dengan Lokasi (Area Bojongsoang/Bandung)
        
        // Merchant 1: Lokasi dekat TULT
        User::create([
            'name' => 'Ayam Geprek Bojong',
            'email' => 'geprek@contoh.com',
            'password' => Hash::make('password'),
            'role' => 'merchant',
            'address' => 'Jl. Telekomunikasi No. 1, Bojongsoang',
            'latitude' => -6.974001, 
            'longitude' => 107.630348, 
        ]);

        // Merchant 2: Lokasi agak ke selatan (PBA)
        User::create([
            'name' => 'Nasi Goreng Mas Yono',
            'email' => 'nasgor@contoh.com',
            'password' => Hash::make('password'),
            'role' => 'merchant',
            'address' => 'Jl. Raya Bojongsoang No. 108',
            'latitude' => -6.976500, 
            'longitude' => 107.632000, 
        ]);

        // Merchant 3: Lokasi agak ke utara (Batununggal)
        User::create([
            'name' => 'Sate Padang Pagi Sore',
            'email' => 'sate@contoh.com',
            'password' => Hash::make('password'),
            'role' => 'merchant',
            'address' => 'Jl. Batununggal Indah Raya',
            'latitude' => -6.960000, 
            'longitude' => 107.625000, 
        ]);

        // Merchant 4: Merchant tanpa lokasi (untuk tes validasi)
        User::create([
            'name' => 'Merchant Baru (Belum Setup Lokasi)',
            'email' => 'new@contoh.com',
            'password' => Hash::make('password'),
            'role' => 'merchant',
            'address' => null,
            'latitude' => null,
            'longitude' => null,
        ]);
    }
}