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
        User::Create([
            'name'=>'Admin User',
            'email' => 'admin@contoh.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);
        User::Create([
            'name'=>'Customer User',
            'email' => 'customer@contoh.com',
            'password' => Hash ::make('password'),
            'role' => 'customer',
        ]);
        User::Create([
            'name'=>'Merchant User',
            'email' => 'Merchant@contoh.com',
            'password' => Hash ::make('password'),
            'role' => 'merchant',
        ]);
    }
}
