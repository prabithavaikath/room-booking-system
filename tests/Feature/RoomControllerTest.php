<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Room;

class RoomControllerTest extends TestCase
{
    /** @test */
    public function guests_can_view_rooms_index()
    {
        $response = $this->get(route('rooms.index'));
        
        $response->assertStatus(200);
        $response->assertViewIs('rooms.index');
    }

    /** @test */
    public function guests_can_view_single_room()
    {
        $room = $this->createRoom();
        
        $response = $this->get(route('rooms.show', $room));
        
        $response->assertStatus(200);
        $response->assertViewIs('rooms.show');
        $response->assertViewHas('room', $room);
    }

    /** @test */
    public function admin_can_create_room()
    {
        $this->loginAsAdmin();
        
        $roomData = [
            'room_number' => '999',
            'type' => 'Suite',
            'price' => 299.99,
            'description' => 'Luxury suite',
            'capacity' => 3,
            'availability_status' => true,
        ];
        
        $response = $this->post(route('rooms.store'), $roomData);
        
        $response->assertRedirect(route('rooms.index'));
        $this->assertDatabaseHas('rooms', ['room_number' => '999']);
    }

    /** @test */
    public function admin_can_update_room()
    {
        $admin = $this->loginAsAdmin();
        $room = $this->createRoom();
        
        $updateData = [
            'room_number' => $room->room_number,
            'type' => 'Double',
            'price' => 199.99,
            'description' => 'Updated description',
            'capacity' => 2,
            'availability_status' => false,
        ];
        
        $response = $this->put(route('rooms.update', $room), $updateData);
        
        $response->assertRedirect(route('rooms.index'));
        $this->assertDatabaseHas('rooms', [
            'id' => $room->id,
            'type' => 'Double',
            'price' => 199.99,
        ]);
    }

    /** @test */
    public function admin_can_delete_room()
    {
        $admin = $this->loginAsAdmin();
        $room = $this->createRoom();
        
        $response = $this->delete(route('rooms.destroy', $room));
        
        $response->assertRedirect(route('rooms.index'));
        $this->assertDatabaseMissing('rooms', ['id' => $room->id]);
    }
}