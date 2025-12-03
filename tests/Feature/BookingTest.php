<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Booking;
use App\Models\Room;
use App\Models\Customer;
use Carbon\Carbon;

class BookingTest extends TestCase
{
    /** @test */
    public function it_can_calculate_nights()
    {
        $checkIn = '2024-01-01';
        $checkOut = '2024-01-05';
        
        $nights = Booking::calculateNights($checkIn, $checkOut);
        
        $this->assertEquals(4, $nights);
    }

    /** @test */
    public function it_can_calculate_total_amount()
    {
        $roomPrice = 100.00;
        $nights = 3;
        
        $total = Booking::calculateTotalAmount($roomPrice, $nights);
        
        $this->assertEquals(300.00, $total);
    }

    /** @test */
    public function it_has_formatted_total_attribute()
    {
        $booking = Booking::factory()->create(['total_amount' => 250.75]);
        
        $this->assertEquals('$250.75', $booking->formatted_total);
    }

    /** @test */
    public function it_has_booking_reference_attribute()
    {
        $booking = Booking::factory()->create(['id' => 123]);
        
        $this->assertEquals('BOOK-000123', $booking->booking_reference);
    }

    /** @test */
    public function it_can_check_if_cancellable()
    {
        $futureBooking = Booking::factory()->create([
            'check_in_date' => Carbon::now()->addHours(48)
        ]);
        
        $pastBooking = Booking::factory()->create([
            'check_in_date' => Carbon::now()->addHours(12)
        ]);
        
        $this->assertTrue($futureBooking->canBeCancelled());
        $this->assertFalse($pastBooking->canBeCancelled());
    }

    /** @test */
    public function it_has_status_badge_attribute()
    {
        $booking = Booking::factory()->create(['status' => 'confirmed']);
        
        $this->assertEquals('success', $booking->statusBadge);
    }
}