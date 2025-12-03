@extends('layouts.app')

@section('title', 'Booking Confirmation - Royal Suites Hotel')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Success Message -->
            <div class="card border-0 shadow-lg mb-5">
                <div class="card-body p-5 text-center">
                    <div class="mb-4">
                        <i class="bi bi-check-circle display-1 text-success"></i>
                    </div>
                    <h1 class="fw-bold mb-3">Booking Confirmed!</h1>
                    <p class="lead mb-4">Thank you for your booking. Your reservation has been confirmed successfully.</p>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        A confirmation email has been sent to <strong>{{ $booking->customer_email }}</strong>
                    </div>
                </div>
            </div>
            
            <!-- Booking Details -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="bi bi-receipt me-2"></i> Booking Details</h4>
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted">Booking Reference</h6>
                            <h4 class="fw-bold text-primary">{{ $booking->booking_reference }}</h4>
                        </div>
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted">Booking Date</h6>
                            <h5>{{ $booking->created_at->format('F d, Y') }}</h5>
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted">Guest Information</h6>
                            <p class="mb-1"><strong>Name:</strong> {{ $booking->customer_name }}</p>
                            <p class="mb-1"><strong>Email:</strong> {{ $booking->customer_email }}</p>
                            <p class="mb-1"><strong>Phone:</strong> {{ $booking->customer_phone }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Stay Details</h6>
                            <p class="mb-1"><strong>Check-in:</strong> {{ $booking->check_in_date->format('F d, Y') }}</p>
                            <p class="mb-1"><strong>Check-out:</strong> {{ $booking->check_out_date->format('F d, Y') }}</p>
                            <p class="mb-1"><strong>Nights:</strong> {{ $booking->total_nights }}</p>
                            <p class="mb-1"><strong>Status:</strong> 
                                <span class="badge bg-{{ $booking->statusBadge }}">
                                    {{ ucfirst($booking->status) }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Room Details -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <h4 class="mb-0"><i class="bi bi-door-closed me-2"></i> Room Details</h4>
                </div>
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-3 text-center">
                            <div class="display-4">
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
                            <p class="text-muted mb-2">{{ $booking->room->description }}</p>
                            <div class="d-flex flex-wrap gap-3">
                                <span class="badge bg-primary">{{ $booking->room->capacity }} person{{ $booking->room->capacity > 1 ? 's' : '' }}</span>
                                @if($booking->room->amenities)
                                <span class="badge bg-secondary">{{ $booking->room->amenities }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Payment Summary -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0"><i class="bi bi-credit-card me-2"></i> Payment Summary</h4>
                </div>
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <td>Room Rate ({{ $booking->total_nights }} nights)</td>
                                    <td class="text-end">${{ number_format($booking->room->price, 2) }} × {{ $booking->total_nights }}</td>
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
                    <div class="alert alert-light mt-3">
                        <i class="bi bi-info-circle me-2"></i>
                        <small>Payment will be collected at the hotel upon check-in. Taxes and fees are included.</small>
                    </div>
                </div>
            </div>
            
            <!-- Special Requests -->
            @if($booking->special_requests)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-warning text-white">
                    <h4 class="mb-0"><i class="bi bi-chat-left-text me-2"></i> Special Requests</h4>
                </div>
                <div class="card-body p-4">
                    <p>{{ $booking->special_requests }}</p>
                </div>
            </div>
            @endif
            
            <!-- Action Buttons -->
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <a href="{{ route('bookings.invoice', $booking) }}" class="btn btn-outline-primary w-100">
                                <i class="bi bi-download me-2"></i> Download Invoice
                            </a>
                        </div>
                        <div class="col-md-4">
                            @if($booking->canBeCancelled())
                            <form action="{{ route('bookings.cancel', $booking) }}" method="POST" class="d-inline w-100">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger w-100" 
                                        onclick="return confirm('Are you sure you want to cancel this booking?')">
                                    <i class="bi bi-x-circle me-2"></i> Cancel Booking
                                </button>
                            </form>
                            @endif
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('home') }}" class="btn btn-success w-100">
                                <i class="bi bi-house me-2"></i> Back to Home
                            </a>
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    
                    <div class="text-center">
                        <h6>Need help with your booking?</h6>
                        <p class="text-muted mb-0">
                            Contact our reservations team at +1 (555) 123-4567 or 
                            <a href="mailto:reservations@royalsuites.com">reservations@royalsuites.com</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection