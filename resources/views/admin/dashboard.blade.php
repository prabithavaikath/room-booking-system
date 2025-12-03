@extends('admin.layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <h1><i class="bi bi-speedometer2"></i> Admin Dashboard</h1>
        <p class="text-muted">Welcome back, {{ auth()->guard('admin')->user()->name }}!</p>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row">
    <div class="col-md-3 mb-3">
        <div class="card text-white bg-primary admin-card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">Total Rooms</h6>
                        <h2 class="card-text">{{ $totalRooms ?? 0 }}</h2>
                    </div>
                    <div>
                        <i class="bi bi-door-closed display-6"></i>
                    </div>
                </div>
                <a href="{{ route('rooms.index') }}" class="text-white">View All →</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card text-white bg-success admin-card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">Available Rooms</h6>
                        <h2 class="card-text">{{ $availableRooms ?? 0 }}</h2>
                    </div>
                    <div>
                        <i class="bi bi-check-circle display-6"></i>
                    </div>
                </div>
                <span class="text-white">({{ $availableRooms > 0 && $totalRooms > 0 ? round(($availableRooms/$totalRooms)*100) : 0 }}%)</span>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card text-white bg-info admin-card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">Total Bookings</h6>
                        <h2 class="card-text">{{ $totalBookings ?? 0 }}</h2>
                    </div>
                    <div>
                        <i class="bi bi-calendar-check display-6"></i>
                    </div>
                </div>
                <span class="text-white">{{ $todayBookings ?? 0 }} today</span>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card text-white bg-warning admin-card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">Total Customers</h6>
                        <h2 class="card-text">{{ $totalCustomers ?? 0 }}</h2>
                    </div>
                    <div>
                        <i class="bi bi-people display-6"></i>
                    </div>
                </div>
                <a href="#" class="text-white">View Customers →</a>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-8">
        <!-- Recent Bookings -->
        <div class="card admin-card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i> Recent Bookings</h5>
            </div>
            <div class="card-body">
                @if(($recentBookings ?? collect())->isEmpty())
                    <div class="alert alert-info">
                        No bookings yet.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Booking ID</th>
                                    <th>Customer</th>
                                    <th>Room</th>
                                    <th>Check-in</th>
                                    <th>Check-out</th>
                                    <th>Status</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentBookings ?? [] as $booking)
                                <tr>
                                    <td>#{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}</td>
                                    <td>
                                        @if($booking->customer)
                                            {{ $booking->customer->full_name }}
                                        @else
                                            {{ $booking->customer_name }}
                                        @endif
                                    </td>
                                    <td>{{ $booking->room->room_number }}</td>
                                    <td>{{ $booking->check_in_date->format('M d') }}</td>
                                    <td>{{ $booking->check_out_date->format('M d') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $booking->statusBadge }}">
                                            {{ ucfirst($booking->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $booking->formatted_total }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Room Statistics -->
        <div class="card admin-card mt-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-pie-chart me-2"></i> Room Statistics</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 text-center">
                        <div class="p-3 border rounded">
                            <h3 class="text-primary">{{ $roomStats['single'] ?? 0 }}</h3>
                            <p class="mb-0">Single Rooms</p>
                            <small class="text-muted">{{ ($roomStats['single'] ?? 0) > 0 && ($totalRooms ?? 0) > 0 ? round((($roomStats['single'] ?? 0)/($totalRooms ?? 1))*100) : 0 }}%</small>
                        </div>
                    </div>
                    <div class="col-md-4 text-center">
                        <div class="p-3 border rounded">
                            <h3 class="text-success">{{ $roomStats['double'] ?? 0 }}</h3>
                            <p class="mb-0">Double Rooms</p>
                            <small class="text-muted">{{ ($roomStats['double'] ?? 0) > 0 && ($totalRooms ?? 0) > 0 ? round((($roomStats['double'] ?? 0)/($totalRooms ?? 1))*100) : 0 }}%</small>
                        </div>
                    </div>
                    <div class="col-md-4 text-center">
                        <div class="p-3 border rounded">
                            <h3 class="text-warning">{{ $roomStats['suite'] ?? 0 }}</h3>
                            <p class="mb-0">Suites</p>
                            <small class="text-muted">{{ ($roomStats['suite'] ?? 0) > 0 && ($totalRooms ?? 0) > 0 ? round((($roomStats['suite'] ?? 0)/($totalRooms ?? 1))*100) : 0 }}%</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <!-- Quick Actions -->
        <div class="card admin-card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-lightning me-2"></i> Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('rooms.create') }}" class="btn btn-primary btn-lg">
                        <i class="bi bi-plus-circle me-2"></i> Add New Room
                    </a>
                    <a href="{{ route('rooms.index') }}" class="btn btn-outline-primary btn-lg">
                        <i class="bi bi-list me-2"></i> Manage Rooms
                    </a>
                    <a href="{{ route('bookings.create') }}" class="btn btn-success btn-lg">
                        <i class="bi bi-calendar-plus me-2"></i> Create Booking
                    </a>
                    <a href="{{ route('admin.profile') }}" class="btn btn-outline-secondary btn-lg">
                        <i class="bi bi-person-circle me-2"></i> My Profile
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Booking Statistics -->
        <div class="card admin-card mt-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-bar-chart me-2"></i> Booking Status</h5>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Confirmed
                        <span class="badge bg-primary rounded-pill">{{ $bookingStats['confirmed'] ?? 0 }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Pending
                        <span class="badge bg-warning rounded-pill">{{ $bookingStats['pending'] ?? 0 }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Checked-in
                        <span class="badge bg-info rounded-pill">{{ $bookingStats['checked_in'] ?? 0 }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Checked-out
                        <span class="badge bg-secondary rounded-pill">{{ $bookingStats['checked_out'] ?? 0 }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Cancelled
                        <span class="badge bg-danger rounded-pill">{{ $bookingStats['cancelled'] ?? 0 }}</span>
                    </li>
                </ul>
            </div>
        </div>
        
        <!-- Recent Customers -->
        <div class="card admin-card mt-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-people me-2"></i> Recent Customers</h5>
            </div>
            <div class="card-body">
                @if(($recentCustomers ?? collect())->isEmpty())
                    <div class="alert alert-info">
                        No customers yet.
                    </div>
                @else
                    <div class="list-group">
                        @foreach($recentCustomers ?? [] as $customer)
                        <div class="list-group-item">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">{{ $customer->full_name }}</h6>
                                <small>{{ $customer->created_at->diffForHumans() }}</small>
                            </div>
                            <p class="mb-1">{{ $customer->email }}</p>
                            <small class="text-muted">{{ $customer->city ?? 'No location' }}</small>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- System Status -->
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card admin-card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-exclamation-triangle me-2"></i> System Status</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="alert alert-success">
                            <h6><i class="bi bi-check-circle"></i> Database</h6>
                            <p class="mb-0">Connected ✓</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="alert alert-success">
                            <h6><i class="bi bi-check-circle"></i> Server</h6>
                            <p class="mb-0">Running ✓</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="alert {{ ($pendingBookings ?? 0) > 0 ? 'alert-warning' : 'alert-success' }}">
                            <h6><i class="bi {{ ($pendingBookings ?? 0) > 0 ? 'bi-exclamation-triangle' : 'bi-check-circle' }}"></i> Pending</h6>
                            <p class="mb-0">{{ $pendingBookings ?? 0 }} pending bookings</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="alert alert-success">
                            <h6><i class="bi bi-check-circle"></i> Rooms</h6>
                            <p class="mb-0">{{ $availableRooms ?? 0 }}/{{ $totalRooms ?? 0 }} available</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection