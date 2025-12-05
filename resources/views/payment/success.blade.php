@extends('layouts.app')

@section('title', 'Payment Successful - Royal Suites Hotel')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-5">
                    <div class="mb-4">
                        <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                    </div>
                    <h2 class="fw-bold mb-3">Payment Successful!</h2>
                    <p class="text-muted mb-4">
                        Thank you for your payment. Your booking has been confirmed.
                    </p>
                    
                    <div class="card bg-light mb-4">
                        <div class="card-body text-start">
                            <h5 class="fw-bold mb-3">Booking Details</h5>
                            <p><strong>Booking ID:</strong> #{{ $booking->id }}</p>
                            <p><strong>Room:</strong> {{ $booking->room->room_number }} - {{ $booking->room->type }}</p>
                            <p><strong>Check-in:</strong> {{ $booking->check_in_date->format('M d, Y') }}</p>
                            <p><strong>Check-out:</strong> {{ $booking->check_out_date->format('M d, Y') }}</p>
                            <p><strong>Total Paid:</strong> ${{ number_format($booking->total_amount, 2) }}</p>
                        </div>
                    </div>
                    
                    <div class="alert alert-info mb-4">
                        <i class="bi bi-info-circle me-2"></i>
                        A confirmation email has been sent to <strong>{{ $booking->customer_email }}</strong>
                    </div>
                    
                    <div class="d-grid gap-2 col-md-6 mx-auto">
                        <a href="{{ route('home') }}" class="btn btn-primary">
                            <i class="bi bi-house me-2"></i> Return to Home
                        </a>
                        <a href="{{ route('bookings.show', $booking) }}" class="btn btn-outline-primary">
                            <i class="bi bi-receipt me-2"></i> View Booking Details
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection