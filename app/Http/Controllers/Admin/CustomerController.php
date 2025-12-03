<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CustomerController extends Controller
{
    /**
     * Display a listing of customers
     */
    public function index(Request $request)
    {
        $query = Customer::query();
        
        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%$search%")
                  ->orWhere('last_name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%")
                  ->orWhere('phone', 'like', "%$search%");
            });
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Order by
        $orderBy = $request->get('order_by', 'created_at');
        $orderDir = $request->get('order_dir', 'desc');
        $query->orderBy($orderBy, $orderDir);
        
        $customers = $query->paginate(20);
        
        // Get customer statistics
        $stats = [
            'total' => Customer::count(),
            'active' => Customer::where('status', 'active')->count(),
            'inactive' => Customer::where('status', 'inactive')->count(),
            'suspended' => Customer::where('status', 'suspended')->count(),
        ];
        
        return view('admin.customers.index', compact('customers', 'stats', 'request'));
    }

    /**
     * Show the form for creating a new customer
     */
    public function create()
    {
        return view('admin.customers.create');
    }

    /**
     * Store a newly created customer
     */
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => 'required|email|unique:customers',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:50',
            'state' => 'nullable|string|max:50',
            'country' => 'nullable|string|max:50',
            'postal_code' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'status' => 'required|in:active,inactive,suspended',
        ]);

        $customer = Customer::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'country' => $request->country,
            'postal_code' => $request->postal_code,
            'date_of_birth' => $request->date_of_birth,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.customers.show', $customer)
            ->with('success', 'Customer created successfully!');
    }

    /**
     * Display the specified customer
     */
    public function show(Customer $customer)
    {
        $customer->load('bookings.room');
        $bookings = $customer->bookings()->orderBy('created_at', 'desc')->paginate(10);
        
        // Get booking statistics for this customer
        $bookingStats = [
            'total' => $customer->bookings()->count(),
            'confirmed' => $customer->bookings()->where('status', 'confirmed')->count(),
            'checked_in' => $customer->bookings()->where('status', 'checked_in')->count(),
            'checked_out' => $customer->bookings()->where('status', 'checked_out')->count(),
            'cancelled' => $customer->bookings()->where('status', 'cancelled')->count(),
        ];
        
        return view('admin.customers.show', compact('customer', 'bookings', 'bookingStats'));
    }

    /**
     * Show the form for editing the specified customer
     */
    public function edit(Customer $customer)
    {
        return view('admin.customers.edit', compact('customer'));
    }

    /**
     * Update the specified customer
     */
    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => 'required|email|unique:customers,email,' . $customer->id,
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:50',
            'state' => 'nullable|string|max:50',
            'country' => 'nullable|string|max:50',
            'postal_code' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'status' => 'required|in:active,inactive,suspended',
        ]);

        $customer->update($request->only([
            'first_name', 'last_name', 'email', 'phone', 'address',
            'city', 'state', 'country', 'postal_code', 'date_of_birth', 'status'
        ]));

        return redirect()->route('admin.customers.show', $customer)
            ->with('success', 'Customer updated successfully!');
    }

    /**
     * Remove the specified customer
     */
    public function destroy(Customer $customer)
    {
        // Check if customer has bookings
        if ($customer->bookings()->count() > 0) {
            return redirect()->route('admin.customers.index')
                ->with('error', 'Cannot delete customer with existing bookings!');
        }

        $customer->delete();

        return redirect()->route('admin.customers.index')
            ->with('success', 'Customer deleted successfully!');
    }

    /**
     * Update customer status
     */
    public function updateStatus(Request $request, Customer $customer)
    {
        $request->validate([
            'status' => 'required|in:active,inactive,suspended',
        ]);

        $customer->status = $request->status;
        $customer->save();

        return redirect()->back()
            ->with('success', 'Customer status updated successfully!');
    }

    /**
     * Reset customer password
     */
    public function resetPassword(Request $request, Customer $customer)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $customer->password = Hash::make($request->password);
        $customer->save();

        return redirect()->back()
            ->with('success', 'Password reset successfully!');
    }

    /**
     * Export customers to CSV
     */
    public function export()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="customers_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers
            fputcsv($file, [
                'Customer ID', 'First Name', 'Last Name', 'Email', 'Phone',
                'Address', 'City', 'State', 'Country', 'Postal Code',
                'Date of Birth', 'Status', 'Registered Date', 'Total Bookings'
            ]);

            $customers = Customer::withCount('bookings')->get();

            foreach ($customers as $customer) {
                fputcsv($file, [
                    $customer->id,
                    $customer->first_name,
                    $customer->last_name,
                    $customer->email,
                    $customer->phone,
                    $customer->address,
                    $customer->city,
                    $customer->state,
                    $customer->country,
                    $customer->postal_code,
                    $customer->date_of_birth,
                    $customer->status,
                    $customer->created_at->format('Y-m-d H:i:s'),
                    $customer->bookings_count
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}