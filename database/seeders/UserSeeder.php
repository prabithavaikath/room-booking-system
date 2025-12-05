<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Customer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin users
        User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@hotel.acom',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Hotel Manager',
            'email' => 'manager@hotel.com',
            'password' => Hash::make('manager123'),
            'role' => 'admin',
        ]);

        // Create sample customers
        Customer::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'phone' => '+1234567890',
            'password' => Hash::make('password123'),
            'address' => '123 Main Street',
            'city' => 'New York',
            'country' => 'USA',
            'status' => 'active',
        ]);

        Customer::create([
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'jane@example.com',
            'phone' => '+0987654321',
            'password' => Hash::make('password123'),
            'address' => '456 Oak Avenue',
            'city' => 'Los Angeles',
            'country' => 'USA',
            'status' => 'active',
        ]);
    }
}