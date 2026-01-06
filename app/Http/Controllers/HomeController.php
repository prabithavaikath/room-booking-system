<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Booking;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Show the home page
     */
    public function index()
    {
        // Get featured rooms (available rooms)
        $featuredRooms = Room::where('availability_status', true)
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();
        
        // Get all room types for filtering
        $roomTypes = Room::select('type')
            ->distinct()
            ->pluck('type');
        
        // Get room statistics
        $totalRooms = Room::count();
        $availableRooms = Room::where('availability_status', true)->count();
        $totalBookings = Booking::count();
        
        //  testimonials
        $testimonials = [
            [
                'name' => 'Sarah Johnson',
                'role' => 'Business Traveler',
                'content' => 'Excellent service! The rooms are clean and comfortable. Booking was seamless.',
                'rating' => 5,
            ],
            [
                'name' => 'Michael Chen',
                'role' => 'Family Vacation',
                'content' => 'Perfect for our family stay. Kids loved the pool and we enjoyed the convenient location.',
                'rating' => 4,
            ],
            [
                'name' => 'Emily Rodriguez',
                'role' => 'Honeymoon',
                'content' => 'The suite was absolutely beautiful. The staff went above and beyond to make our stay special.',
                'rating' => 5,
            ],
        ];
        
        // Get amenities
        $amenities = [
            'Free WiFi',
            'Swimming Pool',
            '24/7 Room Service',
            'Fitness Center',
            'Free Parking',
            'Restaurant',
            'Spa',
            'Conference Rooms',
        ];
        
        return view('home', compact(
            'featuredRooms',
            'roomTypes',
            'totalRooms',
            'availableRooms',
            'totalBookings',
            'testimonials',
            'amenities'
        ));
    }
    
    /**
     * Show about page
     */
    public function about()
    {
        return view('pages.about');
    }
    
    /**
     * Show contact page
     */
    public function contact()
    {
        return view('pages.contact');
    }
    
    /**
     * Show rooms listing page
     */
    public function rooms()
    {
        $rooms = Room::where('availability_status', true)
            ->orderBy('price', 'asc')
            ->get();
        
        return view('pages.rooms', compact('rooms'));
    }
}