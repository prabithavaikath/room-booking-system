<?php

namespace Database\Factories;

use App\Models\Room;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Booking>
 */
class BookingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Generate random check-in date (from today to 60 days in future)
        $checkIn = Carbon::today()->addDays($this->faker->numberBetween(1, 60));
        
        // Generate check-out date (1-14 days after check-in)
        $checkOut = $checkIn->copy()->addDays($this->faker->numberBetween(1, 14));
        
        // Calculate nights
        $nights = $checkOut->diffInDays($checkIn);
        
        // Get or create room
        $room = Room::inRandomOrder()->first() ?? Room::factory()->create();
        
        // Calculate total amount
        $totalAmount = $room->price * $nights;
        
        // Get or create customer
        $customer = Customer::inRandomOrder()->first() ?? Customer::factory()->create();
        
        // Status options with weights
        $statuses = [
            'confirmed' => 50,
            'pending' => 20,
            'checked_in' => 10,
            'checked_out' => 15,
            'cancelled' => 5,
        ];
        
        $status = $this->faker->randomElement(array_keys($statuses));
        
        return [
            'room_id' => $room->id,
            'customer_id' => $customer->id,
            'customer_name' => $customer->first_name . ' ' . $customer->last_name,
            'customer_email' => $customer->email,
            'customer_phone' => $customer->phone ?? $this->faker->phoneNumber(),
            'check_in_date' => $checkIn,
            'check_out_date' => $checkOut,
            'total_nights' => $nights,
            'total_amount' => $totalAmount,
            'status' => $status,
            'special_requests' => $this->faker->boolean(30) ? $this->faker->sentence() : null,
        ];
    }
    
    /**
     * Indicate that the booking is confirmed.
     */
    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'confirmed',
        ]);
    }
    
    /**
     * Indicate that the booking is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }
    
    /**
     * Indicate that the booking is checked in.
     */
    public function checkedIn(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'checked_in',
        ]);
    }
    
    /**
     * Indicate that the booking is checked out.
     */
    public function checkedOut(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'checked_out',
        ]);
    }
    
    /**
     * Indicate that the booking is cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
        ]);
    }
    
    /**
     * Indicate that the booking is upcoming.
     */
    public function upcoming(): static
    {
        return $this->state(function (array $attributes) {
            $checkIn = Carbon::today()->addDays($this->faker->numberBetween(1, 30));
            $checkOut = $checkIn->copy()->addDays($this->faker->numberBetween(1, 7));
            
            return [
                'check_in_date' => $checkIn,
                'check_out_date' => $checkOut,
                'total_nights' => $checkOut->diffInDays($checkIn),
                'status' => 'confirmed',
            ];
        });
    }
    
    /**
     * Indicate that the booking is current (check-in today or past, check-out future).
     */
    public function current(): static
    {
        return $this->state(function (array $attributes) {
            $checkIn = Carbon::today()->subDays($this->faker->numberBetween(0, 5));
            $checkOut = $checkIn->copy()->addDays($this->faker->numberBetween(1, 7));
            
            return [
                'check_in_date' => $checkIn,
                'check_out_date' => $checkOut,
                'total_nights' => $checkOut->diffInDays($checkIn),
                'status' => 'checked_in',
            ];
        });
    }
    
    /**
     * Indicate that the booking is past.
     */
    public function past(): static
    {
        return $this->state(function (array $attributes) {
            $checkOut = Carbon::today()->subDays($this->faker->numberBetween(1, 30));
            $checkIn = $checkOut->copy()->subDays($this->faker->numberBetween(1, 7));
            
            return [
                'check_in_date' => $checkIn,
                'check_out_date' => $checkOut,
                'total_nights' => $checkOut->diffInDays($checkIn),
                'status' => 'checked_out',
            ];
        });
    }
}