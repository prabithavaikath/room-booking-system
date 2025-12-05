@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Payment Cancelled</div>

                <div class="card-body">
                    <div class="alert alert-warning">
                        <h4>Payment Cancelled</h4>
                        <p>Your payment has been cancelled.</p>
                        
                        @if(isset($booking) && $booking)
                            <p><strong>Booking ID:</strong> {{ $booking->id }}</p>
                            <p><strong>Status:</strong> {{ $booking->status }}</p>
                        @endif
                        
                        <hr>
                        <!-- <a href="{{ route('bookings.index') }}" class="btn btn-primary">
                            View Bookings
                        </a> -->
                        <a href="{{ url('/') }}" class="btn btn-secondary">
                            Return to Home
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection