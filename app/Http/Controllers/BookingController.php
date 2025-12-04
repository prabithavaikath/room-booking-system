<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Booking;
use App\Models\Customer;
use App\Http\Requests\StoreBookingRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BookingController extends Controller
{
    /**
     * Show the booking form
     */
    public function create(Request $request)
    {
        $room = null;
        
        // If room ID is provided in query string, pre-select that room
        if ($request->has('room')) {
            $room = Room::find($request->room);
        }
        
        // Get available rooms for the selected dates
        $rooms = Room::where('availability_status', true)->get();
        
        // If authenticated customer, pre-fill their details
        $customer = null;
        if (Auth::guard('customer')->check()) {
            $customer = Auth::guard('customer')->user();
        }
        
        // Set default dates (tomorrow for check-in, day after for check-out)
        $defaultCheckIn = Carbon::tomorrow()->format('Y-m-d');
        $defaultCheckOut = Carbon::tomorrow()->addDay()->format('Y-m-d');
        
        return view('bookings.create', compact('room', 'rooms', 'customer', 'defaultCheckIn', 'defaultCheckOut'));
    }

    /**
     * Check room availability
     */
    public function checkAvailability(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'check_in_date' => 'required|date|after_or_equal:today',
            'check_out_date' => 'required|date|after:check_in_date',
        ]);

        $room = Room::findOrFail($request->room_id);
        
        // Check if room is available
        $isAvailable = $room->isAvailableForDates($request->check_in_date, $request->check_out_date);
        
        if ($isAvailable) {
            // Calculate price
            $nights = Booking::calculateNights($request->check_in_date, $request->check_out_date);
            $totalAmount = Booking::calculateTotalAmount($room->price, $nights);
            
            return response()->json([
                'success' => true,
                'message' => 'Room is available for the selected dates.',
                'data' => [
                    'room' => $room,
                    'nights' => $nights,
                    'price_per_night' => $room->price,
                    'total_amount' => $totalAmount,
                    'formatted_total' => '$' . number_format($totalAmount, 2),
                    'check_in_date' => $request->check_in_date,
                    'check_out_date' => $request->check_out_date,
                ]
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Room is not available for the selected dates. Please choose different dates.',
        ], 400);
    }

    /**
     * Store a new booking
     */
    public function store(StoreBookingRequest $request)
    {
        try {
            // Get the room
            $room = Room::findOrFail($request->room_id);
            
            // Double-check availability
            if (!$room->isAvailableForDates($request->check_in_date, $request->check_out_date)) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Sorry, the room is no longer available for the selected dates. Please choose different dates.');
            }
            
            // Calculate nights and total amount
            $nights = Booking::calculateNights($request->check_in_date, $request->check_out_date);
            $totalAmount = Booking::calculateTotalAmount($room->price, $nights);
            
            // Check if customer is logged in
            $customerId = null;
            if (Auth::guard('customer')->check()) {
                $customer = Auth::guard('customer')->user();
                $customerId = $customer->id;
                
                // Update customer phone if provided and different
                if ($request->customer_phone && $customer->phone != $request->customer_phone) {
                    $customer->phone = $request->customer_phone;
                    $customer->save();
                }
            } else {
                // Check if customer exists by email
                $customer = Customer::where('email', $request->customer_email)->first();
                if ($customer) {
                    $customerId = $customer->id;
                }
            }
            
            // Create the booking
            $booking = Booking::create([
                'room_id' => $room->id,
                'customer_id' => $customerId,
                'customer_name' => $request->customer_name,
                'customer_email' => $request->customer_email,
                'customer_phone' => $request->customer_phone,
                'check_in_date' => $request->check_in_date,
                'check_out_date' => $request->check_out_date,
                'total_nights' => $nights,
                'total_amount' => $totalAmount,
                'status' => 'confirmed',
                'special_requests' => $request->special_requests,
            ]);
            
            // Redirect to confirmation page
            return redirect()->route('bookings.confirmation', $booking)
                ->with('success', 'Booking confirmed successfully!');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while processing your booking. Please try again.');
        }
    }

    /**
     * Show booking confirmation
     */
    public function confirmation(Booking $booking)
    {
        // Verify the booking belongs to the user if they're logged in
        if (Auth::guard('customer')->check()) {
            $customer = Auth::guard('customer')->user();
            if ($booking->customer_id && $booking->customer_id != $customer->id) {
                abort(403, 'Unauthorized access to this booking.');
            }
        }
        
        return view('bookings.confirmation', compact('booking'));
    }

    /**
     * Show booking details
     */
    public function show(Booking $booking)
    {
        // Verify the booking belongs to the user if they're logged in
        if (Auth::guard('customer')->check()) {
            $customer = Auth::guard('customer')->user();
            if ($booking->customer_id && $booking->customer_id != $customer->id) {
                abort(403, 'Unauthorized access to this booking.');
            }
        }
        
        return view('bookings.show', compact('booking'));
    }

    /**
     * Cancel a booking
     */
    public function cancel(Request $request, Booking $booking)
    {
        // Verify the booking belongs to the user if they're logged in
        if (Auth::guard('customer')->check()) {
            $customer = Auth::guard('customer')->user();
            if ($booking->customer_id && $booking->customer_id != $customer->id) {
                abort(403, 'Unauthorized access to this booking.');
            }
        }
        
        // Check if booking can be cancelled
        if (!$booking->canBeCancelled()) {
            return redirect()->back()
                ->with('error', 'This booking cannot be cancelled as check-in is within 24 hours.');
        }
        
        // Update booking status
        $booking->status = 'cancelled';
        $booking->save();
        
        return redirect()->back()
            ->with('success', 'Booking cancelled successfully.');
    }

    /**
     * Download booking invoice (PDF)
     */
    public function invoice(Booking $booking)
    {
        // Verify the booking belongs to the user if they're logged in
        if (Auth::guard('customer')->check()) {
            $customer = Auth::guard('customer')->user();
            if ($booking->customer_id && $booking->customer_id != $customer->id) {
                abort(403, 'Unauthorized access to this booking.');
            }
        }
        
        // For now, we'll just show an invoice page
        // In a real app, you'd generate a PDF here
        return view('bookings.invoice', compact('booking'));
    }

    /**
     * Show user's bookings (for authenticated customers)
     */
    public function myBookings()
    {
        $customer = Auth::guard('customer')->user();
        $bookings = Booking::where('customer_id', $customer->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('bookings.my-bookings', compact('bookings', 'customer'));
    }
}