<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Room>
 */
class RoomFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $roomTypes = ['Single', 'Double', 'Suite'];
        $type = $this->faker->randomElement($roomTypes);
        
        // Set price based on room type
        $prices = [
            'Single' => $this->faker->numberBetween(50, 150),
            'Double' => $this->faker->numberBetween(100, 250),
            'Suite' => $this->faker->numberBetween(200, 500),
        ];
        
        // Set capacity based on room type
        $capacities = [
            'Single' => 1,
            'Double' => 2,
            'Suite' => $this->faker->numberBetween(3, 4),
        ];
        
        // Generate amenities based on room type
        $amenitiesList = [
            'WiFi',
            'TV',
            'Air Conditioning',
            'Mini-fridge',
            'Coffee Maker',
            'Safe',
            'Desk',
            'Hairdryer',
            'Iron',
            'Balcony',
            'Ocean View',
            'City View',
            'Kitchenette',
            'Jacuzzi',
            'Living Area',
        ];
        
        $amenities = [];
        $numAmenities = $type === 'Suite' ? 
            $this->faker->numberBetween(5, 8) : 
            ($type === 'Double' ? 
                $this->faker->numberBetween(3, 5) : 
                $this->faker->numberBetween(2, 4));
        
        for ($i = 0; $i < $numAmenities; $i++) {
            $amenity = $this->faker->randomElement($amenitiesList);
            if (!in_array($amenity, $amenities)) {
                $amenities[] = $amenity;
            }
        }
        
        return [
            'room_number' => $this->faker->unique()->numberBetween(100, 999),
            'type' => $type,
            'price' => $prices[$type],
            'description' => $this->faker->sentence(10),
            'capacity' => $capacities[$type],
            'amenities' => implode(', ', $amenities),
            'availability_status' => $this->faker->boolean(80), // 80% chance of being available
        ];
    }
    
    /**
     * Indicate that the room is available.
     */
    public function available(): static
    {
        return $this->state(fn (array $attributes) => [
            'availability_status' => true,
        ]);
    }
    
    /**
     * Indicate that the room is unavailable.
     */
    public function unavailable(): static
    {
        return $this->state(fn (array $attributes) => [
            'availability_status' => false,
        ]);
    }
    
    /**
     * Indicate that the room is a Single.
     */
    public function single(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'Single',
            'price' => $this->faker->numberBetween(50, 150),
            'capacity' => 1,
        ]);
    }
    
    /**
     * Indicate that the room is a Double.
     */
    public function double(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'Double',
            'price' => $this->faker->numberBetween(100, 250),
            'capacity' => 2,
        ]);
    }
    
    /**
     * Indicate that the room is a Suite.
     */
    public function suite(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'Suite',
            'price' => $this->faker->numberBetween(200, 500),
            'capacity' => $this->faker->numberBetween(3, 4),
        ]);
    }
}