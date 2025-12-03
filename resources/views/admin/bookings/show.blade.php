@extends('admin.layouts.app')

@section('title', 'Booking Details - ' . $booking->booking_reference)

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h1><i class="bi bi-calendar-check"></i> Booking Details</h1>
        <p class="text-muted">Reference: {{ $booking->booking_reference }}</p>
    </div>
    <div class="col-md-4 text-end">
        <div class="btn-group" role="group">
            <a href="{{ route('admin.bookings.edit', $booking) }}" class="btn btn-warning">
                <i class="bi bi-pencil"></i> Edit
            </a>
            <a href="{{ route('bookings.invoice', $booking) }}" class="btn btn-info">
                <i class="bi bi-download"></i> Invoice
            </a>
            <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown">
                <i class="bi bi-gear"></i> Actions
            </button>
            <ul class="dropdown-menu">
                @if($booking->status == 'confirmed')
                <li>
                    <form action="{{ route('admin.bookings.check-in', $booking) }}" method="POST">
                        @csrf
                        <button type="submit" class="dropdown-item">
                            <i class="bi bi-door-open me-2"></i> Check-in
                        </button>
                    </form>
                </li>
                @endif
                @if($booking->status == 'checked_in')
                <li>
                    <form action="{{ route('admin.bookings.check-out', $booking) }}" method="POST">
                        @csrf
                        <button type="submit" class="dropdown-item">
                            <i class="bi bi-door-closed me-2"></i> Check-out
                        </button>
                    </form>
                </li>
                @endif
                @if($booking->status != 'cancelled')
                <li>
                    <form action="{{ route('admin.bookings.cancel', $booking) }}" method="POST">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger" 
                                onclick="return confirm('Are you sure you want to cancel this booking?')">
                            <i class="bi bi-x-circle me-2"></i> Cancel Booking
                        </button>
                    </form>
                </li>
                @endif
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form action="{{ route('admin.bookings.destroy', $booking) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="dropdown-item text-danger" 
                                onclick="return confirm('Are you sure you want to delete this booking? This action cannot be undone.')">
                            <i class="bi bi-trash me-2"></i> Delete Booking
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</div>

<!-- Status Badge -->
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <span class="badge bg-{{ $booking->statusBadge }} p-3 fs-5">
                    <i class="bi bi-circle-fill me-2"></i>{{ ucfirst($booking->status) }}
                </span>
            </div>
            <div class="text-end">
                <p class="mb-0"><strong>Booking Date:</strong> {{ $booking->created_at->format('F d, Y h:i A') }}</p>
                <p class="mb-0"><strong>Last Updated:</strong> {{ $booking->updated_at->format('F d, Y h:i A') }}</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Left Column -->
    <div class="col-md-8">
        <!-- Guest Information -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="bi bi-person-circle me-2"></i> Guest Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <p class="mb-1"><strong>Full Name:</strong> {{ $booking->customer_name }}</p>
                        <p class="mb-1"><strong>Email:</strong> {{ $booking->customer_email }}</p>
                        <p class="mb-1"><strong>Phone:</strong> {{ $booking->customer_phone }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        @if($booking->customer)
                        <p class="mb-1"><strong>Customer ID:</strong> {{ $booking->customer->id }}</p>
                        <p class="mb-1"><strong>Registered:</strong> {{ $booking->customer->created_at->format('F d, Y') }}</p>
                        <p class="mb-1"><strong>Status:</strong> 
                            <span class="badge bg-{{ $booking->customer->status == 'active' ? 'success' : 'danger' }}">
                                {{ ucfirst($booking->customer->status) }}
                            </span>
                        </p>
                        @else
                        <p class="mb-1"><strong>Customer:</strong> <span class="text-muted">Not registered</span></p>
                        @endif
                    </div>
                </div>
                
                @if($booking->customer)
                <div class="mt-3">
                    <a href="{{ route('admin.customers.show', $booking->customer) }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-person me-2"></i> View Customer Profile
                    </a>
                </div>
                @endif
            </div>
        </div>

        <!-- Stay Details -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="bi bi-calendar-event me-2"></i> Stay Details</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-4 mb-3">
                        <div class="p-3 border rounded">
                            <h6 class="text-muted mb-2">Check-in</h6>
                            <h4 class="fw-bold text-primary">{{ $booking->check_in_date->format('d') }}</h4>
                            <p class="mb-0">{{ $booking->check_in_date->format('F Y') }}</p>
                            <small class="text-muted">After 3:00 PM</small>
                            <div class="mt-2">
                                @if($booking->check_in_date->isToday())
                                <span class="badge bg-success">Today</span>
                                @elseif($booking->check_in_date->isFuture())
                                <span class="badge bg-info">Upcoming</span>
                                @else
                                <span class="badge bg-secondary">Past</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="p-3 border rounded">
                            <h6 class="text-muted mb-2">Duration</h6>
                            <h4 class="fw-bold text-success">{{ $booking->total_nights }}</h4>
                            <p class="mb-0">Nights Stay</p>
                            <small class="text-muted">
                                {{ $booking->check_in_date->format('M d') }} - {{ $booking->check_out_date->format('M d') }}
                            </small>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="p-3 border rounded">
                            <h6 class="text-muted mb-2">Check-out</h6>
                            <h4 class="fw-bold text-primary">{{ $booking->check_out_date->format('d') }}</h4>
                            <p class="mb-0">{{ $booking->check_out_date->format('F Y') }}</p>
                            <small class="text-muted">Before 11:00 AM</small>
                            <div class="mt-2">
                                @if($booking->check_out_date->isToday())
                                <span class="badge bg-success">Today</span>
                                @elseif($booking->check_out_date->isFuture())
                                <span class="badge bg-info">Upcoming</span>
                                @else
                                <span class="badge bg-secondary">Past</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column -->
    <div class="col-md-4">
        <!-- Room Information -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="bi bi-door-closed me-2"></i> Room Information</h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
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
                
                <h4 class="text-center">{{ $booking->room->room_number }} - {{ $booking->room->type }}</h4>
                <p class="text-center text-muted">{{ $booking->room->description }}</p>
                
                <div class="d-flex flex-wrap gap-2 justify-content-center mb-3">
                    <span class="badge bg-primary">
                        <i class="bi bi-people me-1"></i> {{ $booking->room->capacity }} person{{ $booking->room->capacity > 1 ? 's' : '' }}
                    </span>
                    <span class="badge bg-secondary">
                        <i class="bi bi-currency-dollar me-1"></i> {{ $booking->room->formatted_price }}/night
                    </span>
                    @if($booking->room->amenities)
                    <span class="badge bg-info">{{ $booking->room->amenities }}</span>
                    @endif
                </div>
                
                <div class="text-center">
                    <a href="{{ route('rooms.show', $booking->room) }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-eye me-2"></i> View Room Details
                    </a>
                </div>
            </div>
        </div>

        <!-- Payment Summary -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="bi bi-credit-card me-2"></i> Payment Summary</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
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
                
                <div class="alert alert-light mt-3">
                    <i class="bi bi-info-circle me-2"></i>
                    <small>
                        @if($booking->status == 'confirmed')
                        Payment due at check-in
                        @elseif($booking->status == 'checked_in')
                        Payment pending
                        @elseif($booking->status == 'checked_out')
                        Payment completed
                        @elseif($booking->status == 'cancelled')
                        Booking cancelled
                        @endif
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Special Requests -->
@if($booking->special_requests)
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="bi bi-chat-left-text me-2"></i> Special Requests</h5>
            </div>
            <div class="card-body">
                <p>{{ $booking->special_requests }}</p>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Status Management -->
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="bi bi-gear me-2"></i> Status Management</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.bookings.update-status', $booking) }}" method="POST" class="row g-3">
                    @csrf
                    <div class="col-md-8">
                        <label class="form-label">Update Booking Status</label>
                        <select name="status" class="form-control" required>
                            <option value="confirmed" {{ $booking->status == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                            <option value="pending" {{ $booking->status == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="checked_in" {{ $booking->status == 'checked_in' ? 'selected' : '' }}>Checked-in</option>
                            <option value="checked_out" {{ $booking->status == 'checked_out' ? 'selected' : '' }}>Checked-out</option>
                            <option value="cancelled" {{ $booking->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <div class="d-grid w-100">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i> Update Status
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Activity Log -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i> Activity Log</h5>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <i class="bi bi-plus-circle text-success me-2"></i>
                            <strong>Booking Created</strong>
                        </div>
                        <span class="text-muted">{{ $booking->created_at->format('M d, Y h:i A') }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <i class="bi bi-pencil text-warning me-2"></i>
                            <strong>Last Updated</strong>
                        </div>
                        <span class="text-muted">{{ $booking->updated_at->format('M d, Y h:i A') }}</span>
                    </li>
                    @if($booking->status == 'checked_in')
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <i class="bi bi-door-open text-info me-2"></i>
                            <strong>Checked In</strong>
                        </div>
                        <span class="text-muted">{{ $booking->updated_at->format('M d, Y h:i A') }}</span>
                    </li>
                    @endif
                    @if($booking->status == 'checked_out')
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <i class="bi bi-door-closed text-secondary me-2"></i>
                            <strong>Checked Out</strong>
                        </div>
                        <span class="text-muted">{{ $booking->updated_at->format('M d, Y h:i A') }}</span>
                    </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection