@extends('admin.layouts.app')

@section('title', 'Bookings Report')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h1><i class="bi bi-calendar-check"></i> Bookings Report</h1>
        <p class="text-muted">Detailed booking information and analytics</p>
    </div>
    <div class="col-md-4 text-end">
        <a href="{{ route('admin.reports.export-bookings') }}" class="btn btn-success">
            <i class="bi bi-download me-2"></i> Export to CSV
        </a>
    </div>
</div>

<!-- Filters -->
<div class="card admin-card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.reports.bookings') }}" id="bookingFilters">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select class="form-control" name="status">
                        <option value="">All Statuses</option>
                        @foreach($statusOptions as $status)
                            <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                {{ ucfirst($status) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Room Type</label>
                    <select class="form-control" name="room_type">
                        <option value="">All Types</option>
                        @foreach($roomTypes as $type)
                            <option value="{{ $type }}" {{ request('room_type') == $type ? 'selected' : '' }}>
                                {{ $type }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">From Date</label>
                    <input type="date" class="form-control" name="date_from" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">To Date</label>
                    <input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}">
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-12">
                    <div class="d-flex justify-content-between">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-filter me-2"></i> Apply Filters
                        </button>
                        <a href="{{ route('admin.reports.bookings') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle me-2"></i> Clear Filters
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Statistics -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <h6 class="card-title">Total Bookings</h6>
                <h2 class="card-text">{{ $totalBookings }}</h2>
                <small>Filtered results</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-success">
            <div class="card-body">
                <h6 class="card-title">Total Revenue</h6>
                <h2 class="card-text">${{ number_format($totalRevenue, 2) }}</h2>
                <small>Filtered results</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-info">
            <div class="card-body">
                <h6 class="card-title">Average Revenue</h6>
                <h2 class="card-text">${{ number_format($averageRevenue, 2) }}</h2>
                <small>Per booking</small>
            </div>
        </div>
    </div>
</div>

<!-- Bookings Table -->
<div class="card admin-card">
    <div class="card-body">
        @if($bookings->isEmpty())
            <div class="alert alert-info text-center py-5">
                <i class="bi bi-calendar-x display-1 text-muted mb-3"></i>
                <h3>No Bookings Found</h3>
                <p class="mb-0">Try adjusting your filters to see more results.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Booking ID</th>
                            <th>Customer</th>
                            <th>Room</th>
                            <th>Dates</th>
                            <th>Nights</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Booking Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bookings as $booking)
                        <tr>
                            <td>
                                <strong>{{ $booking->booking_reference }}</strong>
                            </td>
                            <td>
                                <strong>{{ $booking->customer_name }}</strong><br>
                                <small>{{ $booking->customer_email }}</small>
                            </td>
                            <td>
                                {{ $booking->room->room_number }}<br>
                                <small class="badge bg-{{ $booking->room->type == 'Suite' ? 'warning' : ($booking->room->type == 'Double' ? 'info' : 'primary') }}">
                                    {{ $booking->room->type }}
                                </small>
                            </td>
                            <td>
                                {{ $booking->check_in_date->format('M d') }} - {{ $booking->check_out_date->format('M d') }}<br>
                                <small class="text-muted">{{ $booking->check_in_date->format('Y') }}</small>
                            </td>
                            <td>{{ $booking->total_nights }}</td>
                            <td>
                                <strong>${{ number_format($booking->total_amount, 2) }}</strong>
                            </td>
                            <td>
                                <span class="badge bg-{{ $booking->statusBadge }}">
                                    {{ ucfirst($booking->status) }}
                                </span>
                            </td>
                            <td>
                                {{ $booking->created_at->format('M d, Y') }}<br>
                                <small class="text-muted">{{ $booking->created_at->format('h:i A') }}</small>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('bookings.show', $booking) }}" class="btn btn-sm btn-outline-primary" title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('bookings.invoice', $booking) }}" class="btn btn-sm btn-outline-secondary" title="Invoice">
                                        <i class="bi bi-download"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($bookings->hasPages())
            <div class="mt-4">
                {{ $bookings->links() }}
            </div>
            @endif
        @endif
    </div>
</div>

<!-- Summary -->
@if(!$bookings->isEmpty())
<div class="card admin-card mt-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-file-text me-2"></i> Report Summary</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h6>Filter Summary:</h6>
                <ul class="list-unstyled">
                    <li><strong>Status:</strong> {{ request('status') ? ucfirst(request('status')) : 'All' }}</li>
                    <li><strong>Room Type:</strong> {{ request('room_type') ? request('room_type') : 'All' }}</li>
                    <li><strong>Date Range:</strong> 
                        {{ request('date_from') ? request('date_from') : 'Any' }} 
                        to 
                        {{ request('date_to') ? request('date_to') : 'Any' }}
                    </li>
                </ul>
            </div>
            <div class="col-md-6">
                <h6>Performance Metrics:</h6>
                <ul class="list-unstyled">
                    <li><strong>Total Bookings:</strong> {{ $totalBookings }}</li>
                    <li><strong>Total Revenue:</strong> ${{ number_format($totalRevenue, 2) }}</li>
                    <li><strong>Average per Booking:</strong> ${{ number_format($averageRevenue, 2) }}</li>
                    <li><strong>Report Generated:</strong> {{ now()->format('F d, Y h:i A') }}</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endif
@endsection