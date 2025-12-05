@extends('admin.layouts.app')

@section('title', 'Reports Dashboard')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <h1><i class="bi bi-graph-up"></i> Reports Dashboard</h1>
        <p class="text-muted">Analytics and insights for your hotel business</p>
    </div>
</div>

<!-- Summary Cards -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card text-white bg-primary admin-card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">Monthly Bookings</h6>
                        <h2 class="card-text">{{ $monthlyBookings }}</h2>
                    </div>
                    <div>
                        <i class="bi bi-calendar-check display-6"></i>
                    </div>
                </div>
                <small>This month</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card text-white bg-success admin-card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">Monthly Revenue</h6>
                        <h2 class="card-text">${{ number_format($monthlyRevenue, 0) }}</h2>
                    </div>
                    <div>
                        <i class="bi bi-currency-dollar display-6"></i>
                    </div>
                </div>
                <small>This month</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card text-white bg-info admin-card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">New Customers</h6>
                        <h2 class="card-text">{{ $monthlyCustomers }}</h2>
                    </div>
                    <div>
                        <i class="bi bi-people display-6"></i>
                    </div>
                </div>
                <small>This month</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card text-white bg-warning admin-card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">Occupancy Rate</h6>
                        <h2 class="card-text">{{ number_format($occupancyRate, 1) }}%</h2>
                    </div>
                    <div>
                        <i class="bi bi-building display-6"></i>
                    </div>
                </div>
                <small>{{ $occupiedRooms }}/{{ $totalRooms }} rooms</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Left Column -->
    <div class="col-md-8">
        <!-- Revenue Trend Chart -->
        <div class="card admin-card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-bar-chart me-2"></i> Revenue Trend (Last 6 Months)</h5>
            </div>
            <div class="card-body">
                <div style="height: 300px;">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Top Performing Rooms -->
        <div class="card admin-card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-star me-2"></i> Top Performing Rooms (This Month)</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Room</th>
                                <th>Type</th>
                                <th>Price/Night</th>
                                <th>Bookings</th>
                                <th>Revenue</th>
                                <th>Performance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topRooms as $room)
                            <tr>
                                <td>
                                    <strong>{{ $room->room_number }}</strong>
                                    <br><small class="text-muted">{{ Str::limit($room->description, 30) }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $room->type == 'Suite' ? 'warning' : ($room->type == 'Double' ? 'info' : 'primary') }}">
                                        {{ $room->type }}
                                    </span>
                                </td>
                                <td>${{ number_format($room->price, 2) }}</td>
                                <td>{{ $room->bookings_count }}</td>
                                <td>${{ number_format($room->bookings_count * $room->price, 2) }}</td>
                                <td>
                                    @php
                                        $performance = $room->bookings_count > 0 ? 'High' : 'Low';
                                        $badgeClass = $room->bookings_count >= 3 ? 'success' : ($room->bookings_count >= 1 ? 'warning' : 'secondary');
                                    @endphp
                                    <span class="badge bg-{{ $badgeClass }}">{{ $performance }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Right Column -->
    <div class="col-md-4">
        <!-- Booking Status Distribution -->
        <div class="card admin-card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-pie-chart me-2"></i> Booking Status (This Month)</h5>
            </div>
            <div class="card-body">
                <div style="height: 250px;">
                    <canvas id="statusChart"></canvas>
                </div>
                <div class="mt-3">
                    <ul class="list-group list-group-flush">
                        @foreach($bookingStatuses as $status => $count)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{ ucfirst($status) }}
                            <span class="badge bg-primary rounded-pill">{{ $count }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        
        <!-- Quick Report Links -->
        <div class="card admin-card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-file-earmark-text me-2"></i> Quick Reports</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.reports.bookings') }}" class="btn btn-outline-primary">
                        <i class="bi bi-calendar-check me-2"></i> Bookings Report
                    </a>
                    <!-- <a href="{{ route('admin.reports.revenue') }}" class="btn btn-outline-success">
                        <i class="bi bi-currency-dollar me-2"></i> Revenue Report
                    </a>
                    <a href="{{ route('admin.reports.customers') }}" class="btn btn-outline-info">
                        <i class="bi bi-people me-2"></i> Customers Report
                    </a> -->
                    <a href="{{ route('admin.reports.export-bookings') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-download me-2"></i> Export Bookings
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Recent Bookings -->
        <div class="card admin-card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i> Recent Bookings</h5>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    @foreach($recentBookings as $booking)
                    <div class="list-group-item">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">{{ $booking->customer_name }}</h6>
                            <small class="text-muted">{{ $booking->created_at->diffForHumans() }}</small>
                        </div>
                        <p class="mb-1">
                            {{ $booking->room->room_number }} • ${{ number_format($booking->total_amount, 2) }}
                        </p>
                        <small>
                            <span class="badge bg-{{ $booking->statusBadge }}">{{ ucfirst($booking->status) }}</span>
                        </small>
                    </div>
                    @endforeach
                </div>
                <div class="text-center mt-3">
                    <a href="{{ route('admin.reports.bookings') }}" class="btn btn-sm btn-outline-primary">
                        View All Bookings
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Revenue Trend Chart
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        const revenueLabels = {!! json_encode(array_keys($revenueTrend)) !!};
        const revenueData = {!! json_encode(array_values($revenueTrend)) !!};
        
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: revenueLabels,
                datasets: [{
                    label: 'Monthly Revenue ($)',
                    data: revenueData,
                    borderColor: '#3498db',
                    backgroundColor: 'rgba(52, 152, 219, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value;
                            }
                        }
                    }
                }
            }
        });
        
        // Booking Status Chart
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        const statusLabels = {!! json_encode(array_keys($bookingStatuses)) !!};
        const statusData = {!! json_encode(array_values($bookingStatuses)) !!};
        const statusColors = {
            'confirmed': '#28a745',
            'pending': '#ffc107',
            'cancelled': '#dc3545',
            'checked_in': '#17a2b8',
            'checked_out': '#6c757d'
        };
        
        const statusBackgroundColors = statusLabels.map(label => statusColors[label] || '#6c757d');
        
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: statusLabels.map(label => label.charAt(0).toUpperCase() + label.slice(1)),
                datasets: [{
                    data: statusData,
                    backgroundColor: statusBackgroundColors,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    });
</script>
@endsection