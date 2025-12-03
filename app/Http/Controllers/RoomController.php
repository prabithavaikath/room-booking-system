<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoomController extends Controller
{
    /**
     * Display a listing of rooms.
     */
    public function index()
    {
        $rooms = Room::orderBy('room_number')->get();
        return view('rooms.index', compact('rooms'));
    }

    /**
     * Show the form for creating a new room.
     */
    public function create()
    {
        return view('rooms.create');
    }

    /**
     * Store a newly created room in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_number' => 'required|unique:rooms|max:50',
            'type' => 'required|in:Single,Double,Suite',
            'price' => 'required|numeric|min:0|max:999999.99',
            'description' => 'nullable|string|max:1000',
            'capacity' => 'required|integer|min:1|max:10',
            'amenities' => 'nullable|string|max:500',
            'availability_status' => 'boolean',
        ]);

        $validated['availability_status'] = $request->has('availability_status');

        Room::create($validated);

        return redirect()->route('rooms.index')
            ->with('success', 'Room created successfully!');
    }

    /**
     * Display the specified room.
     */
    public function show(Room $room)
    {
        $bookings = $room->bookings()->orderBy('check_in_date', 'desc')->get();
        return view('rooms.show', compact('room', 'bookings'));
    }

    /**
     * Show the form for editing the specified room.
     */
    public function edit(Room $room)
    {
        return view('rooms.edit', compact('room'));
    }

    /**
     * Update the specified room in storage.
     */
    public function update(Request $request, Room $room)
    {
        $validated = $request->validate([
            'room_number' => [
                'required',
                'max:50',
                Rule::unique('rooms')->ignore($room->id)
            ],
            'type' => 'required|in:Single,Double,Suite',
            'price' => 'required|numeric|min:0|max:999999.99',
            'description' => 'nullable|string|max:1000',
            'capacity' => 'required|integer|min:1|max:10',
            'amenities' => 'nullable|string|max:500',
            'availability_status' => 'boolean',
        ]);

        $validated['availability_status'] = $request->has('availability_status');

        $room->update($validated);

        return redirect()->route('rooms.index')
            ->with('success', 'Room updated successfully!');
    }

    /**
     * Remove the specified room from storage.
     */
    public function destroy(Room $room)
    {
        // Check if room has any bookings
        if ($room->bookings()->count() > 0) {
            return redirect()->route('rooms.index')
                ->with('error', 'Cannot delete room with existing bookings!');
        }

        $room->delete();

        return redirect()->route('rooms.index')
            ->with('success', 'Room deleted successfully!');
    }

    /**
     * Toggle room availability status.
     */
    public function toggleAvailability(Room $room)
    {
        $room->availability_status = !$room->availability_status;
        $room->save();

        $status = $room->availability_status ? 'available' : 'unavailable';
        return redirect()->route('rooms.index')
            ->with('success', "Room marked as {$status}!");
    }
}