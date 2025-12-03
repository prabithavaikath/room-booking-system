<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Show admin dashboard
     */
    public function index()
    {
        // Get statistics
        $totalRooms = Room::count();
        $availableRooms = Room::where('availability_status', true)->count();
        $totalBookings = Booking::count();
        $todayBookings = Booking::whereDate('created_at', Carbon::today())->count();
        $totalCustomers = Customer::count();
        $pendingBookings = Booking::where('status', 'pending')->count();
        
        // Get recent bookings
        $recentBookings = Booking::with(['room', 'customer'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
        
        // Get recent customers
        $recentCustomers = Customer::orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        // Get booking statistics by status
        $bookingStats = [
            'confirmed' => Booking::where('status', 'confirmed')->count(),
            'pending' => Booking::where('status', 'pending')->count(),
            'cancelled' => Booking::where('status', 'cancelled')->count(),
            'checked_in' => Booking::where('status', 'checked_in')->count(),
            'checked_out' => Booking::where('status', 'checked_out')->count(),
        ];
        
        // Get room type statistics
        $roomStats = [
            'single' => Room::where('type', 'Single')->count(),
            'double' => Room::where('type', 'Double')->count(),
            'suite' => Room::where('type', 'Suite')->count(),
        ];
        
        return view('admin.dashboard', compact(
            'totalRooms',
            'availableRooms',
            'totalBookings',
            'todayBookings',
            'totalCustomers',
            'pendingBookings',
            'recentBookings',
            'recentCustomers',
            'bookingStats',
            'roomStats'
        ));
    }
    
    /**
     * Show admin profile
     */
    public function profile()
    {
        $admin = auth()->guard('admin')->user();
        return view('admin.profile', compact('admin'));
    }
    
    /**
     * Update admin profile
     */
    public function updateProfile(Request $request)
    {
        $admin = auth()->guard('admin')->user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $admin->id,
            'current_password' => 'nullable|current_password:admin',
            'new_password' => 'nullable|min:8|confirmed',
        ]);
        
        $admin->name = $validated['name'];
        $admin->email = $validated['email'];
        
        if ($request->filled('new_password')) {
            $admin->password = bcrypt($validated['new_password']);
        }
        
        $admin->save();
        
        return redirect()->route('admin.profile')
            ->with('success', 'Profile updated successfully!');
    }
}