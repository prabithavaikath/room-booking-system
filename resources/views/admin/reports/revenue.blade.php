@extends('admin.layouts.app')

@section('title', 'Revenue Report')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h1><i class="bi bi-currency-dollar"></i> Revenue Report</h1>
        <p class="text-muted">Financial analytics and revenue tracking</p>
    </div>
    <div class="col-md-4 text-end">
        <a href="{{ route('admin.reports.export-revenue') }}?start_date={{ $startDate }}&end_date={{ $endDate }}" 
           class="btn btn-success">
            <i class="bi bi-download me-2"></i> Export to CSV
        </a>
    </div>
</div>

<!-- Date Filter -->
<div class="card admin-card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.reports.revenue') }}" id="revenueFilters">
            <div class="row g-3">
                <div class="col-md-5">
                    <label class="form-label">Start Date</label>
                    <input type="date" class="form-control" name="start_date" value="{{ $startDate }}">
                </div>
                <div class="col-md-5">
                    <label class="form-label">End Date</label>
                    <input type="date" class="form-control" name="end_date" value="{{ $endDate }}">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-filter me-2"></i> Apply
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Summary Statistics -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <h6 class="card-title">Total Revenue</h6>
                <h2 class="card-text">${{ number_format($totalRevenue, 2) }}</h2>
                <small>{{ $startDate }} to {{ $endDate }}</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-success">
            <div class="card-body">
                <h6 class="card-title">Total Bookings</h6>
                <h2 class="card-text">{{ $totalBookings }}</h2>
                <small>{{ $startDate }} to {{ $endDate }}</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-info">
            <div class="card-body">
                <h6 class="card-title">Average Revenue</h6>
                <h2 class="card-text">${{ number_format($averageRevenue, 2) }}</h2>
                <small>Per booking</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-warning">
            <div class="card-body">
                <h6 class="card-title">Period</h6>
                <h4 class="card-text">
                    {{ \Carbon\Carbon::parse($startDate)->format('M d') }} - 
                    {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}
                </h4>
                <small>{{ $dailyRevenue->count() }} days</small>
            </div>
        </div>
    </div>
</div>

<!-- Daily Revenue Chart -->
<div class="card admin-card mb-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-bar-chart me-2"></i> Daily Revenue Trend</h5>
    </div>
    <div class="card-body">
        <div style="height: 300px;">
            <canvas id="dailyRevenueChart"></canvas>
        </div>
    </div>
</div>

<div class="row">
    <!-- Left Column - Daily Revenue Table -->
    <div class="col-md-8">
        <div class="card admin-card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-calendar-day me-2"></i> Daily Revenue Details</h5>
            </div>
            <div class="card-body">
                @if($dailyRevenue->isEmpty())
                    <div class="alert alert-info text-center py-5">
                        <i class="bi bi-calendar-x display-1 text-muted mb-3"></i>
                        <h3>No Revenue Data</h3>
                        <p class="mb-0">No bookings found for the selected period.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Revenue ($)</th>
                                    <th>Bookings</th>
                                    <th>Average per Booking</th>
                                    <th>Trend</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($dailyRevenue as $day)
                                @php
                                    $avg = $day->bookings > 0 ? $day->revenue / $day->bookings : 0;
                                @endphp
                                <tr>
                                    <td>
                                        <strong>{{ \Carbon\Carbon::parse($day->date)->format('M d, Y') }}</strong><br>
                                        <small class="text-muted">{{ \Carbon\Carbon::parse($day->date)->format('l') }}</small>
                                    </td>
                                    <td>
                                        <strong>${{ number_format($day->revenue, 2) }}</strong>
                                    </td>
                                    <td>{{ $day->bookings }}</td>
                                    <td>${{ number_format($avg, 2) }}</td>
                                    <td>
                                        @php
                                            if ($day->bookings >= 5) {
                                                $trend = 'High';
                                                $badge = 'success';
                                            } elseif ($day->bookings >= 2) {
                                                $trend = 'Medium';
                                                $badge = 'warning';
                                            } else {
                                                $trend = 'Low';
                                                $badge = 'secondary';
                                            }
                                        @endphp
                                        <span class="badge bg-{{ $badge }}">{{ $trend }}</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-success">
                                <tr>
                                    <td><strong>TOTAL</strong></td>
                                    <td><strong>${{ number_format($totalRevenue, 2) }}</strong></td>
                                    <td><strong>{{ $totalBookings }}</strong></td>
                                    <td><strong>${{ number_format($averageRevenue, 2) }}</strong></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Right Column - Revenue by Category -->
    <div class="col-md-4">
        <!-- Revenue by Room Type -->
        <div class="card admin-card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-door-closed me-2"></i> Revenue by Room Type</h5>
            </div>
            <div class="card-body">
                @if($revenueByRoomType->isEmpty())
                    <div class="alert alert-info">
                        No revenue data by room type.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th class="text-end">Revenue</th>
                                    <th class="text-end">Bookings</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($revenueByRoomType as $item)
                                <tr>
                                    <td>
                                        <span class="badge bg-{{ $item->type == 'Suite' ? 'warning' : ($item->type == 'Double' ? 'info' : 'primary') }}">
                                            {{ $item->type }}
                                        </span>
                                    </td>
                                    <td class="text-end">${{ number_format($item->revenue, 2) }}</td>
                                    <td class="text-end">{{ $item->bookings }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Room Type Chart -->
                    <div style="height: 200px; margin-top: 20px;">
                        <canvas id="roomTypeChart"></canvas>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Revenue by Status -->
        <div class="card admin-card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-pie-chart me-2"></i> Revenue by Booking Status</h5>
            </div>
            <div class="card-body">
                @if($revenueByStatus->isEmpty())
                    <div class="alert alert-info">
                        No revenue data by status.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Status</th>
                                    <th class="text-end">Revenue</th>
                                    <th class="text-end">Bookings</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($revenueByStatus as $item)
                                <tr>
                                    <td>
                                        <span class="badge bg-{{ $item->statusBadge ?? 'secondary' }}">
                                            {{ ucfirst($item->status) }}
                                        </span>
                                    </td>
                                    <td class="text-end">${{ number_format($item->revenue, 2) }}</td>
                                    <td class="text-end">{{ $item->bookings }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Daily Revenue Chart
        const dailyCtx = document.getElementById('dailyRevenueChart').getContext('2d');
        const dailyLabels = {!! json_encode($dailyRevenue->pluck('date')->map(function($date) {
            return \Carbon\Carbon::parse($date)->format('M d');
        })) !!};
        const dailyRevenueData = {!! json_encode($dailyRevenue->pluck('revenue')) !!};
        const dailyBookingsData = {!! json_encode($dailyRevenue->pluck('bookings')) !!};
        
        new Chart(dailyCtx, {
            type: 'bar',
            data: {
                labels: dailyLabels,
                datasets: [
                    {
                        label: 'Revenue ($)',
                        data: dailyRevenueData,
                        backgroundColor: 'rgba(52, 152, 219, 0.7)',
                        borderColor: '#3498db',
                        borderWidth: 1,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Bookings',
                        data: dailyBookingsData,
                        backgroundColor: 'rgba(40, 167, 69, 0.2)',
                        borderColor: '#28a745',
                        borderWidth: 1,
                        type: 'line',
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                stacked: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Revenue ($)'
                        },
                        ticks: {
                            callback: function(value) {
                                return '$' + value;
                            }
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: 'Bookings'
                        },
                        grid: {
                            drawOnChartArea: false,
                        },
                    }
                }
            }
        });
        
        // Room Type Chart
        @if(!$revenueByRoomType->isEmpty())
        const roomTypeCtx = document.getElementById('roomTypeChart').getContext('2d');
        const roomTypeLabels = {!! json_encode($revenueByRoomType->pluck('type')) !!};
        const roomTypeData = {!! json_encode($revenueByRoomType->pluck('revenue')) !!};
        const roomTypeColors = ['#ffc107', '#17a2b8', '#007bff'];
        
        new Chart(roomTypeCtx, {
            type: 'doughnut',
            data: {
                labels: roomTypeLabels,
                datasets: [{
                    data: roomTypeData,
                    backgroundColor: roomTypeColors,
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
        @endif
    });
</script>
@endsection