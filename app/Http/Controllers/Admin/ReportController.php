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
    public function bookings(Request $request)
    {
        $query = Booking::with(['room', 'customer']);
        
        // Apply filters
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        if ($request->has('date_from') && $request->date_from) {
            $query->where('check_in_date', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && $request->date_to) {
            $query->where('check_out_date', '<=', $request->date_to);
        }
        
        if ($request->has('room_type') && $request->room_type) {
            $query->whereHas('room', function($q) use ($request) {
                $q->where('type', $request->room_type);
            });
        }
        
        $bookings = $query->orderBy('created_at', 'desc')->paginate(20);
        
        // Statistics for the filtered results
        $totalRevenue = $query->sum('total_amount');
        $totalBookings = $query->count();
        $averageRevenue = $totalBookings > 0 ? $totalRevenue / $totalBookings : 0;
        
        // Get room types for filter
        $roomTypes = Room::select('type')->distinct()->pluck('type');
        
        // Get status options
        $statusOptions = ['confirmed', 'pending', 'cancelled', 'checked_in', 'checked_out'];
        
        return view('admin.reports.bookings', compact(
            'bookings',
            'totalRevenue',
            'totalBookings',
            'averageRevenue',
            'roomTypes',
            'statusOptions'
        ));
    }
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
     * Show revenue reports
     */
    public function revenue(Request $request)
    {
        // Default to current month
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        
        // Get daily revenue for the period
        $dailyRevenue = Booking::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, SUM(total_amount) as revenue, COUNT(*) as bookings')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        // Get revenue by room type
        $revenueByRoomType = Booking::whereBetween('created_at', [$startDate, $endDate])
            ->join('rooms', 'bookings.room_id', '=', 'rooms.id')
            ->selectRaw('rooms.type, SUM(bookings.total_amount) as revenue, COUNT(*) as bookings')
            ->groupBy('rooms.type')
            ->orderBy('revenue', 'desc')
            ->get();
        
        // Get revenue by status
        $revenueByStatus = Booking::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('status, SUM(total_amount) as revenue, COUNT(*) as bookings')
            ->groupBy('status')
            ->orderBy('revenue', 'desc')
            ->get();
        
        // Calculate totals
        $totalRevenue = $dailyRevenue->sum('revenue');
        $totalBookings = $dailyRevenue->sum('bookings');
        $averageRevenue = $totalBookings > 0 ? $totalRevenue / $totalBookings : 0;
        
        return view('admin.reports.revenue', compact(
            'dailyRevenue',
            'revenueByRoomType',
            'revenueByStatus',
            'totalRevenue',
            'totalBookings',
            'averageRevenue',
            'startDate',
            'endDate'
        ));
    }
    
    /**
     * Show customer reports
     */
    public function customers()
    {
        // Get all customers with their booking statistics
        $customers = Customer::withCount(['bookings'])
            ->withSum('bookings', 'total_amount')
            ->orderBy('bookings_count', 'desc')
            ->paginate(20);
        
        // Customer statistics
        $totalCustomers = Customer::count();
        $activeCustomers = Customer::where('status', 'active')->count();
        $totalBookings = Booking::count();
        $avgBookingsPerCustomer = $totalCustomers > 0 ? $totalBookings / $totalCustomers : 0;
        
        // Get customer registration trend (last 6 months)
        $registrationTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthStart = $month->copy()->startOfMonth();
            $monthEnd = $month->copy()->endOfMonth();
            
            $registrations = Customer::whereBetween('created_at', [$monthStart, $monthEnd])->count();
            $registrationTrend[$month->format('M Y')] = $registrations;
        }
        
        return view('admin.reports.customers', compact(
            'customers',
            'totalCustomers',
            'activeCustomers',
            'totalBookings',
            'avgBookingsPerCustomer',
            'registrationTrend'
        ));
    }
    
    /**
     * Export bookings to CSV
     */
    public function exportBookings(Request $request)
    {
        $bookings = Booking::with(['room', 'customer'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        $filename = 'bookings_' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($bookings) {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers
            fputcsv($file, [
                'Booking ID',
                'Customer Name',
                'Customer Email',
                'Room Number',
                'Room Type',
                'Check-in Date',
                'Check-out Date',
                'Nights',
                'Total Amount',
                'Status',
                'Booking Date'
            ]);
            
            // Add data rows
            foreach ($bookings as $booking) {
                fputcsv($file, [
                    $booking->booking_reference,
                    $booking->customer_name,
                    $booking->customer_email,
                    $booking->room->room_number,
                    $booking->room->type,
                    $booking->check_in_date->format('Y-m-d'),
                    $booking->check_out_date->format('Y-m-d'),
                    $booking->total_nights,
                    $booking->total_amount,
                    ucfirst($booking->status),
                    $booking->created_at->format('Y-m-d H:i:s')
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * Export revenue report to CSV
     */
    public function exportRevenue(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        
        $dailyRevenue = Booking::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, SUM(total_amount) as revenue, COUNT(*) as bookings')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        $filename = 'revenue_report_' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($dailyRevenue, $startDate, $endDate) {
            $file = fopen('php://output', 'w');
            
            // Add report header
            fputcsv($file, ['Revenue Report', 'Period: ' . $startDate . ' to ' . $endDate]);
            fputcsv($file, []); // Empty row
            fputcsv($file, ['Date', 'Revenue ($)', 'Bookings']);
            
            // Add data rows
            $totalRevenue = 0;
            $totalBookings = 0;
            
            foreach ($dailyRevenue as $day) {
                fputcsv($file, [
                    $day->date,
                    number_format($day->revenue, 2),
                    $day->bookings
                ]);
                
                $totalRevenue += $day->revenue;
                $totalBookings += $day->bookings;
            }
            
            // Add totals row
            fputcsv($file, []); // Empty row
            fputcsv($file, ['TOTAL', number_format($totalRevenue, 2), $totalBookings]);
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}