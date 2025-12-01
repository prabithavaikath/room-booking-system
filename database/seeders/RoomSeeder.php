<?php

namespace Database\Seeders;

use App\Models\Room;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        $rooms = [
            [
                'room_number' => '101',
                'type' => 'Single',
                'price' => 99.99,
                'description' => 'Cozy single room with city view',
                'capacity' => 1,
                'amenities' => 'WiFi, TV, Mini-fridge',
                'availability_status' => true,
            ],
            [
                'room_number' => '102',
                'type' => 'Single',
                'price' => 89.99,
                'description' => 'Standard single room',
                'capacity' => 1,
                'amenities' => 'WiFi, TV',
                'availability_status' => true,
            ],
            [
                'room_number' => '201',
                'type' => 'Double',
                'price' => 149.99,
                'description' => 'Spacious double room with balcony',
                'capacity' => 2,
                'amenities' => 'WiFi, TV, Mini-bar, Balcony',
                'availability_status' => true,
            ],
            [
                'room_number' => '202',
                'type' => 'Double',
                'price' => 139.99,
                'description' => 'Comfortable double room',
                'capacity' => 2,
                'amenities' => 'WiFi, TV, Work desk',
                'availability_status' => true,
            ],
            [
                'room_number' => '301',
                'type' => 'Suite',
                'price' => 299.99,
                'description' => 'Luxury suite with living area',
                'capacity' => 4,
                'amenities' => 'WiFi, Smart TV, Jacuzzi, Kitchenette, Living room',
                'availability_status' => true,
            ],
            [
                'room_number' => '302',
                'type' => 'Suite',
                'price' => 249.99,
                'description' => 'Executive suite',
                'capacity' => 3,
                'amenities' => 'WiFi, TV, Mini-bar, Office area',
                'availability_status' => true,
            ],
        ];

        foreach ($rooms as $room) {
            Room::create($room);
        }
    }
}