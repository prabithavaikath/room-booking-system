@extends('customer.layouts.app')

@section('title', 'Customer Dashboard')

@section('content')
<div class="welcome-banner">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h2 class="mb-2">Welcome back, {{ $customer->first_name }}! 👋</h2>
            <p class="mb-0">Manage your bookings and profile from your personal dashboard.</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('bookings.create') }}" class="btn btn-light me-2">
                <i class="bi bi-plus-circle me-2"></i> New Booking
            </a>
            <a href="{{ route('bookings.my-bookings') }}" class="btn btn-outline-light">
                <i class="bi bi-calendar-check me-2"></i> My Bookings
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-3 mb-4">
        <div class="card customer-card text-white bg-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Total Bookings</h6>
                        <h2 class="card-text">{{ $customer->bookings()->count() }}</h2>
                    </div>
                    <div>
                        <i class="bi bi-calendar-check display-6"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card customer-card text-white bg-success">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Active Bookings</h6>
                        <h2 class="card-text">{{ $customer->bookings()->where('status', 'confirmed')->count() }}</h2>
                    </div>
                    <div>
                        <i class="bi bi-check-circle display-6"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card customer-card text-white bg-info">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Pending</h6>
                        <h2 class="card-text">{{ $customer->bookings()->where('status', 'pending')->count() }}</h2>
                    </div>
                    <div>
                        <i class="bi bi-clock display-6"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card customer-card text-white bg-warning">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Member Since</h6>
                        <h4 class="card-text">{{ $customer->created_at->format('M Y') }}</h4>
                    </div>
                    <div>
                        <i class="bi bi-person-badge display-6"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card customer-card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i> Recent Bookings</h5>
            </div>
            <div class="card-body">
                @if($bookings->isEmpty())
                    <div class="text-center py-4">
                        <i class="bi bi-calendar-x display-1 text-muted"></i>
                        <h5 class="mt-3">No Bookings Yet</h5>
                        <p class="text-muted">You haven't made any bookings yet.</p>
                        <a href="{{ route('bookings.create') }}" class="btn btn-primary">Make Your First Booking</a>
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
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bookings as $booking)
                                <tr>
                                    <td>#{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}</td>
                                    <td>{{ $booking->room->room_number }} ({{ $booking->room->type }})</td>
                                    <td>{{ $booking->check_in_date->format('M d, Y') }}</td>
                                    <td>{{ $booking->check_out_date->format('M d, Y') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $booking->statusBadge }}">
                                            {{ ucfirst($booking->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="text-center mt-3">
                        <a href="{{ route('customer.bookings') }}" class="btn btn-outline-primary">
                            View All Bookings <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card customer-card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-person-circle me-2"></i> Profile Summary</h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <div class="mb-3">
                        <i class="bi bi-person-circle" style="font-size: 4rem; color: #667eea;"></i>
                    </div>
                    <h5>{{ $customer->full_name }}</h5>
                    <p class="text-muted">{{ $customer->email }}</p>
                </div>
                
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between">
                        <span>Phone</span>
                        <strong>{{ $customer->phone ?? 'Not set' }}</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span>Location</span>
                        <strong>{{ $customer->city ?? 'Not set' }}, {{ $customer->country ?? 'Not set' }}</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span>Account Status</span>
                        <span class="badge bg-success">Active</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span>Member Since</span>
                        <strong>{{ $customer->created_at->format('F d, Y') }}</strong>
                    </li>
                </ul>
                
                <div class="mt-3">
                    <a href="{{ route('customer.profile') }}" class="btn btn-primary w-100">
                        <i class="bi bi-pencil me-2"></i> Edit Profile
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection