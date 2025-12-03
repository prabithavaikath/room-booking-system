@extends('layouts.app')

@section('title', 'Booking Details - Royal Suites Hotel')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="fw-bold">Booking Details</h1>
                    <p class="text-muted mb-0">Reference: {{ $booking->booking_reference }}</p>
                </div>
                <div>
                    <span class="badge bg-{{ $booking->statusBadge }} p-3 fs-6">
                        {{ ucfirst($booking->status) }}
                    </span>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-3">
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('bookings.invoice', $booking) }}" class="btn btn-outline-primary">
                            <i class="bi bi-download me-2"></i> Download Invoice
                        </a>
                        @if($booking->canBeCancelled())
                        <form action="{{ route('bookings.cancel', $booking) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger" 
                                    onclick="return confirm('Are you sure you want to cancel this booking?')">
                                <i class="bi bi-x-circle me-2"></i> Cancel Booking
                            </button>
                        </form>
                        @endif
                        <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-house me-2"></i> Back to Home
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Guest Information -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="bi bi-person-circle me-2"></i> Guest Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <p class="mb-1"><strong>Full Name:</strong> {{ $booking->customer_name }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <p class="mb-1"><strong>Email:</strong> {{ $booking->customer_email }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <p class="mb-1"><strong>Phone:</strong> {{ $booking->customer_phone }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <p class="mb-1"><strong>Booking Date:</strong> {{ $booking->created_at->format('F d, Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Stay Details -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="bi bi-calendar-check me-2"></i> Stay Details</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="text-center p-3 border rounded">
                                <h6 class="text-muted mb-2">Check-in</h6>
                                <h4 class="fw-bold text-primary">{{ $booking->check_in_date->format('d') }}</h4>
                                <p class="mb-0">{{ $booking->check_in_date->format('M Y') }}</p>
                                <small class="text-muted">After 3:00 PM</small>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="text-center p-3 border rounded">
                                <h6 class="text-muted mb-2">Nights</h6>
                                <h4 class="fw-bold text-success">{{ $booking->total_nights }}</h4>
                                <p class="mb-0">Nights Stay</p>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="text-center p-3 border rounded">
                                <h6 class="text-muted mb-2">Check-out</h6>
                                <h4 class="fw-bold text-primary">{{ $booking->check_out_date->format('d') }}</h4>
                                <p class="mb-0">{{ $booking->check_out_date->format('M Y') }}</p>
                                <small class="text-muted">Before 11:00 AM</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Room Information -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="bi bi-door-closed me-2"></i> Room Information</h5>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-3 text-center mb-3">
                            <div class="display-1">
                                @if($booking->room->type == 'Suite')
                                    🏨
                                @elseif($booking->room->type == 'Double')
                                    👥
                                @else
                                    👤
                                @endif
                            </div>
                        </div>
                        <div class="col-md-9">
                            <h4 class="fw-bold">{{ $booking->room->room_number }} - {{ $booking->room->type }}</h4>
                            <p class="text-muted">{{ $booking->room->description }}</p>
                            <div class="d-flex flex-wrap gap-2 mb-3">
                                <span class="badge bg-primary">
                                    <i class="bi bi-people me-1"></i> {{ $booking->room->capacity }} person{{ $booking->room->capacity > 1 ? 's' : '' }}
                                </span>
                                @if($booking->room->amenities)
                                <span class="badge bg-secondary">{{ $booking->room->amenities }}</span>
                                @endif
                            </div>
                            <a href="{{ route('rooms.show', $booking->room) }}" class="btn btn-sm btn-outline-primary">
                                View Room Details
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Payment Details -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="bi bi-credit-card me-2"></i> Payment Details</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <td>Room Rate per Night</td>
                                    <td class="text-end">${{ number_format($booking->room->price, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>Number of Nights</td>
                                    <td class="text-end">{{ $booking->total_nights }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Subtotal</strong></td>
                                    <td class="text-end">${{ number_format($booking->room->price * $booking->total_nights, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>Tax (10%)</td>
                                    <td class="text-end">${{ number_format($booking->total_amount * 0.1, 2) }}</td>
                                </tr>
                                <tr class="table-success">
                                    <td><strong>Total Amount</strong></td>
                                    <td class="text-end fw-bold">${{ number_format($booking->total_amount * 1.1, 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="alert alert-info mt-3">
                        <i class="bi bi-info-circle me-2"></i>
                        <small>
                            Payment will be collected at the hotel upon check-in. 
                            @if($booking->canBeCancelled())
                            Free cancellation available until {{ $booking->check_in_date->subDay()->format('F d, Y h:i A') }}.
                            @endif
                        </small>
                    </div>
                </div>
            </div>
            
            <!-- Special Requests -->
            @if($booking->special_requests)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="bi bi-chat-left-text me-2"></i> Special Requests</h5>
                </div>
                <div class="card-body">
                    <p>{{ $booking->special_requests }}</p>
                </div>
            </div>
            @endif
            
            <!-- Help Section -->
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-5">
                    <i class="bi bi-question-circle display-1 text-muted mb-4"></i>
                    <h4 class="fw-bold mb-3">Need Assistance?</h4>
                    <p class="text-muted mb-4">Our team is available 24/7 to help with your booking.</p>
                    <div class="d-flex justify-content-center gap-3">
                        <a href="tel:+15551234567" class="btn btn-outline-primary">
                            <i class="bi bi-telephone me-2"></i> Call Us
                        </a>
                        <a href="mailto:reservations@royalsuites.com" class="btn btn-outline-success">
                            <i class="bi bi-envelope me-2"></i> Email Us
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection