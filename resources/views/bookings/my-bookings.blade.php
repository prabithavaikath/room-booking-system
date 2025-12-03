@extends('customer.layouts.app')

@section('title', 'My Bookings')

@section('content')
<div class="welcome-banner">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h2 class="mb-2">My Bookings</h2>
            <p class="mb-0">View and manage all your bookings</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('bookings.create') }}" class="btn btn-light">
                <i class="bi bi-plus-circle me-2"></i> New Booking
            </a>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card customer-card mt-4">
    <div class="card-body">
        @if($bookings->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-calendar-x display-1 text-muted mb-4"></i>
                <h3>No Bookings Yet</h3>
                <p class="text-muted mb-4">You haven't made any bookings yet.</p>
                <a href="{{ route('bookings.create') }}" class="btn btn-primary btn-lg">
                    <i class="bi bi-plus-circle me-2"></i> Make Your First Booking
                </a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Booking ID</th>
                            <th>Room</th>
                            <th>Check-in</th>
                            <th>Check-out</th>
                            <th>Nights</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bookings as $booking)
                        <tr>
                            <td>
                                <strong>{{ $booking->booking_reference }}</strong><br>
                                <small class="text-muted">{{ $booking->created_at->format('M d, Y') }}</small>
                            </td>
                            <td>
                                {{ $booking->room->room_number }}<br>
                                <small class="text-muted">{{ $booking->room->type }}</small>
                            </td>
                            <td>
                                {{ $booking->check_in_date->format('M d, Y') }}<br>
                                <small class="text-muted">After 3:00 PM</small>
                            </td>
                            <td>
                                {{ $booking->check_out_date->format('M d, Y') }}<br>
                                <small class="text-muted">Before 11:00 AM</small>
                            </td>
                            <td>{{ $booking->total_nights }}</td>
                            <td>{{ $booking->formatted_total }}</td>
                            <td>
                                <span class="badge bg-{{ $booking->statusBadge }}">
                                    {{ ucfirst($booking->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('bookings.show', $booking) }}" class="btn btn-sm btn-outline-primary" title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('bookings.invoice', $booking) }}" class="btn btn-sm btn-outline-secondary" title="Invoice">
                                        <i class="bi bi-download"></i>
                                    </a>
                                    @if($booking->canBeCancelled() && $booking->status == 'confirmed')
                                    <form action="{{ route('bookings.cancel', $booking) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Cancel"
                                                onclick="return confirm('Are you sure you want to cancel this booking?')">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    </form>
                                    @endif
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

<!-- Booking Stats -->
@if(!$bookings->isEmpty())
<div class="row mt-4">
    <div class="col-md-3">
        <div class="card customer-card text-white bg-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Total Bookings</h6>
                        <h2 class="card-text">{{ $bookings->total() }}</h2>
                    </div>
                    <div>
                        <i class="bi bi-calendar-check display-6"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card customer-card text-white bg-success">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Confirmed</h6>
                        <h2 class="card-text">{{ $bookings->where('status', 'confirmed')->count() }}</h2>
                    </div>
                    <div>
                        <i class="bi bi-check-circle display-6"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card customer-card text-white bg-info">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Upcoming</h6>
                        <h2 class="card-text">{{ $bookings->where('check_in_date', '>', now())->count() }}</h2>
                    </div>
                    <div>
                        <i class="bi bi-clock display-6"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card customer-card text-white bg-secondary">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Completed</h6>
                        <h2 class="card-text">{{ $bookings->where('status', 'checked_out')->count() }}</h2>
                    </div>
                    <div>
                        <i class="bi bi-check2-all display-6"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection