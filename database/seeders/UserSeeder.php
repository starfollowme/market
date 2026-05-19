<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::updateOrCreate(
            ['email' => 'admin@market.com'],
            [
                'name'     => 'Admin Market',
                'password' => Hash::make('admin123'),
                'role'     => 'admin',
                'phone'    => '081234567890',
                'address'  => 'Jl. Admin No. 1',
            ]
        );

        // Demo customer
        User::updateOrCreate(
            ['email' => 'customer@market.com'],
            [
                'name'     => 'Customer Demo',
                'password' => Hash::make('customer123'),
                'role'     => 'customer',
                'phone'    => '089876543210',
                'address'  => 'Jl. Customer No. 2',
            ]
        );
    }
}
