<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Room;
use App\Models\Customer;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BookingController extends Controller
{
    /**
     * Display a listing of bookings with filters
     */
    public function index(Request $request)
    {
        $query = Booking::with(['room', 'customer']);
        
        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('room_id')) {
            $query->where('room_id', $request->room_id);
        }
        
        if ($request->filled('customer_name')) {
            $query->where('customer_name', 'like', '%' . $request->customer_name . '%');
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('check_in_date', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('check_out_date', '<=', $request->date_to);
        }
        
        // Get upcoming bookings
        if ($request->has('upcoming')) {
            $query->where('check_in_date', '>=', Carbon::today());
        }
        
        // Get current bookings
        if ($request->has('current')) {
            $query->where('check_in_date', '<=', Carbon::today())
                  ->where('check_out_date', '>=', Carbon::today());
        }
        
        // Order by
        $orderBy = $request->get('order_by', 'created_at');
        $orderDir = $request->get('order_dir', 'desc');
        $query->orderBy($orderBy, $orderDir);
        
        $bookings = $query->paginate(20);
        
        // Get rooms for filter dropdown
        $rooms = Room::orderBy('room_number')->get();
        
        // Get booking statistics
        $stats = [
            'total' => Booking::count(),
            'confirmed' => Booking::where('status', 'confirmed')->count(),
            'pending' => Booking::where('status', 'pending')->count(),
            'checked_in' => Booking::where('status', 'checked_in')->count(),
            'checked_out' => Booking::where('status', 'checked_out')->count(),
            'cancelled' => Booking::where('status', 'cancelled')->count(),
        ];
        
        return view('admin.bookings.index', compact('bookings', 'rooms', 'stats', 'request'));
    }

    /**
     * Show the form for creating a new booking (admin)
     */
    public function create()
    {
        $rooms = Room::where('availability_status', true)->get();
        $customers = Customer::orderBy('first_name')->get();
        
        return view('admin.bookings.create', compact('rooms', 'customers'));
    }

    /**
     * Store a newly created booking (admin)
     */
    public function store(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'customer_id' => 'nullable|exists:customers,id',
            'customer_name' => 'required|string|max:100',
            'customer_email' => 'required|email|max:100',
            'customer_phone' => 'required|string|max:20',
            'check_in_date' => 'required|date|after_or_equal:today',
            'check_out_date' => 'required|date|after:check_in_date',
            'status' => 'required|in:confirmed,pending,cancelled,checked_in,checked_out',
            'special_requests' => 'nullable|string|max:500',
        ]);

        // Check room availability
        $room = Room::findOrFail($request->room_id);
        if (!$room->isAvailableForDates($request->check_in_date, $request->check_out_date)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Room is not available for the selected dates.');
        }

        // Calculate nights and total amount
        $nights = Booking::calculateNights($request->check_in_date, $request->check_out_date);
        $totalAmount = Booking::calculateTotalAmount($room->price, $nights);

        // Create booking
        $booking = Booking::create([
            'room_id' => $request->room_id,
            'customer_id' => $request->customer_id,
            'customer_name' => $request->customer_name,
            'customer_email' => $request->customer_email,
            'customer_phone' => $request->customer_phone,
            'check_in_date' => $request->check_in_date,
            'check_out_date' => $request->check_out_date,
            'total_nights' => $nights,
            'total_amount' => $totalAmount,
            'status' => $request->status,
            'special_requests' => $request->special_requests,
        ]);

        return redirect()->route('admin.bookings.show', $booking)
            ->with('success', 'Booking created successfully!');
    }

    /**
     * Display the specified booking
     */
    public function show(Booking $booking)
    {
        $booking->load(['room', 'customer']);
        return view('admin.bookings.show', compact('booking'));
    }

    /**
     * Show the form for editing the specified booking
     */
    public function edit(Booking $booking)
    {
        $rooms = Room::all();
        $customers = Customer::orderBy('first_name')->get();
        $booking->load(['room', 'customer']);
        
        return view('admin.bookings.edit', compact('booking', 'rooms', 'customers'));
    }

    /**
     * Update the specified booking
     */
    public function update(Request $request, Booking $booking)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'customer_id' => 'nullable|exists:customers,id',
            'customer_name' => 'required|string|max:100',
            'customer_email' => 'required|email|max:100',
            'customer_phone' => 'required|string|max:20',
            'check_in_date' => 'required|date',
            'check_out_date' => 'required|date|after:check_in_date',
            'status' => 'required|in:confirmed,pending,cancelled,checked_in,checked_out',
            'special_requests' => 'nullable|string|max:500',
        ]);

        // Recalculate if dates or room changed
        if ($request->room_id != $booking->room_id || 
            $request->check_in_date != $booking->check_in_date ||
            $request->check_out_date != $booking->check_out_date) {
            
            $room = Room::findOrFail($request->room_id);
            $nights = Booking::calculateNights($request->check_in_date, $request->check_out_date);
            $totalAmount = Booking::calculateTotalAmount($room->price, $nights);
            
            $booking->room_id = $request->room_id;
            $booking->check_in_date = $request->check_in_date;
            $booking->check_out_date = $request->check_out_date;
            $booking->total_nights = $nights;
            $booking->total_amount = $totalAmount;
        }

        $booking->customer_id = $request->customer_id;
        $booking->customer_name = $request->customer_name;
        $booking->customer_email = $request->customer_email;
        $booking->customer_phone = $request->customer_phone;
        $booking->status = $request->status;
        $booking->special_requests = $request->special_requests;
        
        $booking->save();

        return redirect()->route('admin.bookings.show', $booking)
            ->with('success', 'Booking updated successfully!');
    }

    /**
     * Remove the specified booking
     */
    public function destroy(Booking $booking)
    {
        $booking->delete();

        return redirect()->route('admin.bookings.index')
            ->with('success', 'Booking deleted successfully!');
    }

    /**
     * Update booking status
     */
    public function updateStatus(Request $request, Booking $booking)
    {
        $request->validate([
            'status' => 'required|in:confirmed,pending,cancelled,checked_in,checked_out',
        ]);

        $booking->status = $request->status;
        $booking->save();

        return redirect()->back()
            ->with('success', 'Booking status updated successfully!');
    }

    /**
     * Check-in a booking
     */
    public function checkIn(Booking $booking)
    {
        $booking->status = 'checked_in';
        $booking->save();

        return redirect()->back()
            ->with('success', 'Guest checked in successfully!');
    }

    /**
     * Check-out a booking
     */
    public function checkOut(Booking $booking)
    {
        $booking->status = 'checked_out';
        $booking->save();

        return redirect()->back()
            ->with('success', 'Guest checked out successfully!');
    }

    /**
     * Cancel a booking
     */
    public function cancel(Booking $booking)
    {
        $booking->status = 'cancelled';
        $booking->save();

        return redirect()->back()
            ->with('success', 'Booking cancelled successfully!');
    }

    /**
     * Show booking calendar view
     */
    public function calendar(Request $request)
    {
        $bookings = Booking::with('room')
            ->whereBetween('check_in_date', [Carbon::today(), Carbon::today()->addDays(30)])
            ->orWhereBetween('check_out_date', [Carbon::today(), Carbon::today()->addDays(30)])
            ->get();
        
        $rooms = Room::all();
        
        return view('admin.bookings.calendar', compact('bookings', 'rooms'));
    }

    /**
     * Export bookings to CSV
     */
    public function export(Request $request)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="bookings_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function() use ($request) {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers
            fputcsv($file, [
                'Booking ID', 'Customer Name', 'Customer Email', 'Customer Phone',
                'Room Number', 'Room Type', 'Check-in', 'Check-out', 'Nights',
                'Total Amount', 'Status', 'Booking Date', 'Special Requests'
            ]);

            // Apply filters if any
            $query = Booking::with('room');
            
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
            
            if ($request->filled('date_from')) {
                $query->whereDate('check_in_date', '>=', $request->date_from);
            }
            
            if ($request->filled('date_to')) {
                $query->whereDate('check_out_date', '<=', $request->date_to);
            }

            $bookings = $query->get();

            foreach ($bookings as $booking) {
                fputcsv($file, [
                    $booking->booking_reference,
                    $booking->customer_name,
                    $booking->customer_email,
                    $booking->customer_phone,
                    $booking->room->room_number,
                    $booking->room->type,
                    $booking->check_in_date->format('Y-m-d'),
                    $booking->check_out_date->format('Y-m-d'),
                    $booking->total_nights,
                    $booking->total_amount,
                    ucfirst($booking->status),
                    $booking->created_at->format('Y-m-d H:i:s'),
                    $booking->special_requests
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}