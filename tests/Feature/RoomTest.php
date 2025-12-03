<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Room;
use App\Models\Booking;
use Carbon\Carbon;

class RoomTest extends TestCase
{
    /** @test */
    public function it_can_create_a_room()
    {
        $room = Room::create([
            'room_number' => '101',
            'type' => 'Single',
            'price' => 99.99,
            'description' => 'Test room',
            'capacity' => 1,
            'availability_status' => true,
        ]);

        $this->assertDatabaseHas('rooms', [
            'room_number' => '101',
            'type' => 'Single',
            'price' => 99.99,
        ]);
    }

    /** @test */
    public function it_has_formatted_price_attribute()
    {
        $room = Room::factory()->create(['price' => 150.50]);
        
        $this->assertEquals('$150.50', $room->formatted_price);
    }

    /** @test */
    public function it_can_check_availability_for_dates()
    {
        $room = $this->createRoom();
        
        $checkIn = Carbon::tomorrow();
        $checkOut = Carbon::tomorrow()->addDays(2);
        
        // Room should be available initially
        $this->assertTrue($room->isAvailableForDates($checkIn, $checkOut));
        
        // Create a booking for those dates
        Booking::create([
            'room_id' => $room->id,
            'customer_name' => 'Test Customer',
            'customer_email' => 'test@example.com',
            'customer_phone' => '1234567890',
            'check_in_date' => $checkIn,
            'check_out_date' => $checkOut,
            'total_nights' => 2,
            'total_amount' => 200.00,
            'status' => 'confirmed',
        ]);
        
        // Room should no longer be available
        $this->assertFalse($room->isAvailableForDates($checkIn, $checkOut));
    }

    /** @test */
    public function unavailable_room_returns_false_for_availability()
    {
        $room = $this->createRoom(['availability_status' => false]);
        
        $this->assertFalse($room->isAvailableForDates(
            Carbon::tomorrow(),
            Carbon::tomorrow()->addDays(2)
        ));
    }
}