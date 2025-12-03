<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use App\Models\User;
use App\Models\Customer;
use App\Models\Room;
use App\Models\Booking;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Create an admin user
     */
    protected function createAdminUser()
    {
        return User::factory()->create([
            'email' => 'admin@hotel.com',
            'role' => 'admin',
            'password' => bcrypt('password123'),
        ]);
    }

    /**
     * Create a customer
     */
    protected function createCustomer()
    {
        return Customer::factory()->create([
            'email' => 'customer@example.com',
            'password' => bcrypt('password123'),
        ]);
    }

    /**
     * Create a room
     */
    protected function createRoom($attributes = [])
    {
        $defaults = [
            'room_number' => 'TEST' . rand(100, 999),
            'type' => 'Single',
            'price' => 100.00,
            'availability_status' => true,
        ];

        return Room::factory()->create(array_merge($defaults, $attributes));
    }

    /**
     * Create a booking
     */
    protected function createBooking($attributes = [])
    {
        $defaults = [
            'check_in_date' => now()->addDays(1),
            'check_out_date' => now()->addDays(3),
            'status' => 'confirmed',
        ];

        return Booking::factory()->create(array_merge($defaults, $attributes));
    }

    /**
     * Login as admin
     */
    protected function loginAsAdmin()
    {
        $admin = $this->createAdminUser();
        $this->actingAs($admin, 'admin');
        return $admin;
    }

    /**
     * Login as customer
     */
    protected function loginAsCustomer()
    {
        $customer = $this->createCustomer();
        $this->actingAs($customer, 'customer');
        return $customer;
    }
}