<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Booking;
use App\Models\Customer;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // Define date range - default to current month
        $currentMonth = now()->format('Y-m');
        $startDate = now()->startOfMonth();
        $endDate = now()->endOfMonth();
        
        // If custom dates are provided in request
        if ($request->has('start_date') && $request->has('end_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();
        }
        
        // 1. Monthly Bookings
        $monthlyBookings = Booking::whereBetween('created_at', [$startDate, $endDate])->count();
        
        // 2. Monthly Revenue
        $monthlyRevenue = Booking::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', '!=', 'cancelled')
            ->sum('total_amount');
        
        // 3. Monthly New Customers
        $monthlyCustomers = Customer::whereBetween('created_at', [$startDate, $endDate])->count();
        
        // 4. Occupancy Rate
        $totalRooms = Room::count();
        $occupiedRooms = Booking::whereBetween('created_at', [$startDate, $endDate])
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->distinct('room_id')
            ->count('room_id');
        $occupancyRate = $totalRooms > 0 ? ($occupiedRooms / $totalRooms) * 100 : 0;
        
        // 5. Top Performing Rooms (This Month)
        $topRooms = Room::withCount(['bookings' => function($query) use ($startDate, $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate])
                  ->where('status', '!=', 'cancelled');
        }])
        ->orderBy('bookings_count', 'desc')
        ->take(5)
        ->get();
        
        // 6. Booking Status Distribution (This Month)
        $bookingStatuses = Booking::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
        
        // Ensure all statuses are present (even with 0 count)
        $allStatuses = ['pending', 'confirmed', 'cancelled', 'checked_in', 'checked_out'];
        foreach ($allStatuses as $status) {
            if (!isset($bookingStatuses[$status])) {
                $bookingStatuses[$status] = 0;
            }
        }
        
        // 7. Revenue Trend (Last 6 Months)
        $revenueTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthStart = $month->copy()->startOfMonth();
            $monthEnd = $month->copy()->endOfMonth();
            
            $revenue = Booking::whereBetween('created_at', [$monthStart, $monthEnd])
                ->where('status', '!=', 'cancelled')
                ->sum('total_amount');
            
            $revenueTrend[$month->format('M Y')] = $revenue;
        }
        
        // 8. Recent Bookings (Last 10 bookings)
        $recentBookings = Booking::with(['room', 'customer'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->map(function($booking) {
                // Add formatted attributes for the view
                $booking->customer_name = $booking->customer->name ?? 'N/A';
                $booking->statusBadge = $this->getStatusBadge($booking->status);
                return $booking;
            });

        return view('admin.reports.index', compact(
            'monthlyBookings',
            'monthlyRevenue',
            'monthlyCustomers',
            'totalRooms',
            'occupiedRooms',
            'occupancyRate',
            'topRooms',
            'bookingStatuses',
            'revenueTrend',
            'recentBookings'
        ));
    }
    
    /**
     * Get Bootstrap badge class for booking status
     */
    private function getStatusBadge($status)
    {
        $badgeClasses = [
            'pending' => 'warning',
            'confirmed' => 'success',
            'cancelled' => 'danger',
            'checked_in' => 'info',
            'checked_out' => 'secondary'
        ];
        
        return $badgeClasses[$status] ?? 'secondary';
    }
    
    /**
     * Bookings Report
     */
    public function bookingsReport(Request $request)
    {
        $query = Booking::with(['room', 'customer']);
        
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('created_at', [
                Carbon::parse($request->start_date)->startOfDay(),
                Carbon::parse($request->end_date)->endOfDay()
            ]);
        }
        
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        $bookings = $query->orderBy('created_at', 'desc')->paginate(20);
        
        return view('admin.reports.bookings', compact('bookings'));
    }
    
    /**
     * Revenue Report
     */
    public function revenueReport(Request $request)
    {
        $query = Booking::where('status', '!=', 'cancelled');
        
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('created_at', [
                Carbon::parse($request->start_date)->startOfDay(),
                Carbon::parse($request->end_date)->endOfDay()
            ]);
        }
        
        $revenueByMonth = $query->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(total_amount) as revenue')
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->get();
            
        $totalRevenue = $query->sum('total_amount');
        
        return view('admin.reports.revenue', compact('revenueByMonth', 'totalRevenue'));
    }
    
    /**
     * Customers Report
     */
    public function customersReport(Request $request)
    {
        $query = Customer::withCount(['bookings' => function($q) {
            $q->where('status', '!=', 'cancelled');
        }])->withSum(['bookings' => function($q) {
            $q->where('status', '!=', 'cancelled');
        }], 'total_amount');
        
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('created_at', [
                Carbon::parse($request->start_date)->startOfDay(),
                Carbon::parse($request->end_date)->endOfDay()
            ]);
        }
        
        $customers = $query->orderBy('created_at', 'desc')->paginate(20);
        
        return view('admin.reports.customers', compact('customers'));
    }
    
    /**
     * Export Bookings
     */
    public function exportBookings(Request $request)
    {
        // Add your export logic here
        return response()->download(storage_path('app/exports/bookings.csv'));
    }
}