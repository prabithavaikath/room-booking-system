@extends('layouts.app')

@section('title', 'Invoice - ' . $booking->booking_reference)

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Invoice Header -->
            <div class="card border-0 shadow-lg mb-4">
                <div class="card-body p-5">
                    <div class="row">
                        <div class="col-md-6">
                            <h1 class="fw-bold text-primary mb-0">INVOICE</h1>
                            <p class="text-muted mb-0">Booking Reference: {{ $booking->booking_reference }}</p>
                            <p class="text-muted">Date: {{ $booking->created_at->format('F d, Y') }}</p>
                        </div>
                        <div class="col-md-6 text-end">
                            <h2 class="fw-bold mb-0">Royal Suites Hotel</h2>
                            <p class="text-muted mb-0">123 Luxury Avenue</p>
                            <p class="text-muted mb-0">New York, NY 10001</p>
                            <p class="text-muted">+1 (555) 123-4567</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Billing Information -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted">BILL TO:</h6>
                            <h5 class="fw-bold">{{ $booking->customer_name }}</h5>
                            <p class="mb-1">{{ $booking->customer_email }}</p>
                            <p class="mb-0">{{ $booking->customer_phone }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">BOOKING DETAILS:</h6>
                            <p class="mb-1"><strong>Booking Date:</strong> {{ $booking->created_at->format('F d, Y') }}</p>
                            <p class="mb-1"><strong>Check-in:</strong> {{ $booking->check_in_date->format('F d, Y') }}</p>
                            <p class="mb-1"><strong>Check-out:</strong> {{ $booking->check_out_date->format('F d, Y') }}</p>
                            <p class="mb-0"><strong>Nights:</strong> {{ $booking->total_nights }}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Invoice Items -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="py-3">DESCRIPTION</th>
                                    <th class="text-center py-3">QUANTITY</th>
                                    <th class="text-center py-3">UNIT PRICE</th>
                                    <th class="text-center py-3">AMOUNT</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="py-3">
                                        <strong>{{ $booking->room->room_number }} - {{ $booking->room->type }}</strong><br>
                                        <small class="text-muted">{{ $booking->room->description }}</small>
                                    </td>
                                    <td class="text-center py-3">{{ $booking->total_nights }} nights</td>
                                    <td class="text-center py-3">${{ number_format($booking->room->price, 2) }}</td>
                                    <td class="text-center py-3">${{ number_format($booking->room->price * $booking->total_nights, 2) }}</td>
                                </tr>
                                <!-- Tax Row -->
                                <tr>
                                    <td colspan="3" class="text-end py-3">
                                        <strong>Subtotal</strong>
                                    </td>
                                    <td class="text-center py-3">${{ number_format($booking->room->price * $booking->total_nights, 2) }}</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-end py-3">
                                        <strong>Tax (10%)</strong>
                                    </td>
                                    <td class="text-center py-3">${{ number_format($booking->total_amount * 0.1, 2) }}</td>
                                </tr>
                                <!-- Total Row -->
                                <tr class="table-success">
                                    <td colspan="3" class="text-end py-3">
                                        <h5 class="fw-bold mb-0">TOTAL</h5>
                                    </td>
                                    <td class="text-center py-3">
                                        <h5 class="fw-bold mb-0">${{ number_format($booking->total_amount * 1.1, 2) }}</h5>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Payment Terms -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3">PAYMENT TERMS</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-2"><strong>Payment Status:</strong> 
                                <span class="badge bg-{{ $booking->statusBadge }}">
                                    {{ ucfirst($booking->status) }}
                                </span>
                            </p>
                            <p class="mb-2"><strong>Payment Method:</strong> Pay at Hotel</p>
                            <p class="mb-0"><strong>Due Date:</strong> Upon Check-in</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-2"><strong>Payment Instructions:</strong></p>
                            <p class="text-muted small mb-0">
                                Payment will be collected at the hotel upon check-in. We accept all major credit cards, debit cards, and cash.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Notes -->
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3">NOTES</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <p class="small text-muted mb-2">
                                • This is a computer-generated invoice and does not require a signature.<br>
                                • Please present this invoice at check-in.<br>
                                • Cancellation policy: Free cancellation up to 48 hours before check-in.
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p class="small text-muted mb-2">
                                • Check-in time: 3:00 PM<br>
                                • Check-out time: 11:00 AM<br>
                                • Contact: reservations@royalsuites.com
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Print Button -->
            <div class="text-center mt-5">
                <button onclick="window.print()" class="btn btn-primary btn-lg">
                    <i class="bi bi-printer me-2"></i> Print Invoice
                </button>
                <a href="{{ route('bookings.show', $booking) }}" class="btn btn-outline-secondary btn-lg ms-3">
                    <i class="bi bi-arrow-left me-2"></i> Back to Booking
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    @media print {
        .navbar, .footer, .btn {
            display: none !important;
        }
        
        body {
            padding: 0 !important;
            margin: 0 !important;
        }
        
        .container {
            max-width: 100% !important;
            padding: 0 !important;
        }
        
        .card {
            border: none !important;
            box-shadow: none !important;
        }
        
        .table {
            border: 1px solid #dee2e6;
        }
    }
</style>
@endsection