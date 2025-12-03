@extends('admin.layouts.app')

@section('title', 'Booking Management')

@section('content')
<div class="row mb-4">
    <div class="col-md-6">
        <h1><i class="bi bi-calendar-check"></i> Booking Management</h1>
        <p class="text-muted">Manage all bookings in the system</p>
    </div>
    <div class="col-md-6 text-end">
        <a href="{{ route('admin.bookings.create') }}" class="btn btn-success">
            <i class="bi bi-plus-circle"></i> Create Booking
        </a>
        <a href="{{ route('admin.bookings.calendar') }}" class="btn btn-info">
            <i class="bi bi-calendar-week"></i> Calendar View
        </a>
        <a href="{{ route('admin.bookings.export') }}?{{ http_build_query(request()->query()) }}" class="btn btn-secondary">
            <i class="bi bi-download"></i> Export CSV
        </a>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-2 mb-3">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body">
                <h6 class="text-muted">Total</h6>
                <h3 class="fw-bold">{{ $stats['total'] }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-2 mb-3">
        <div class="card border-0 shadow-sm text-center border-top border-success">
            <div class="card-body">
                <h6 class="text-muted">Confirmed</h6>
                <h3 class="fw-bold text-success">{{ $stats['confirmed'] }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-2 mb-3">
        <div class="card border-0 shadow-sm text-center border-top border-warning">
            <div class="card-body">
                <h6 class="text-muted">Pending</h6>
                <h3 class="fw-bold text-warning">{{ $stats['pending'] }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-2 mb-3">
        <div class="card border-0 shadow-sm text-center border-top border-info">
            <div class="card-body">
                <h6 class="text-muted">Checked-in</h6>
                <h3 class="fw-bold text-info">{{ $stats['checked_in'] }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-2 mb-3">
        <div class="card border-0 shadow-sm text-center border-top border-secondary">
            <div class="card-body">
                <h6 class="text-muted">Checked-out</h6>
                <h3 class="fw-bold text-secondary">{{ $stats['checked_out'] }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-2 mb-3">
        <div class="card border-0 shadow-sm text-center border-top border-danger">
            <div class="card-body">
                <h6 class="text-muted">Cancelled</h6>
                <h3 class="fw-bold text-danger">{{ $stats['cancelled'] }}</h3>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.bookings.index') }}" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-control">
                    <option value="">All Status</option>
                    <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="checked_in" {{ request('status') == 'checked_in' ? 'selected' : '' }}>Checked-in</option>
                    <option value="checked_out" {{ request('status') == 'checked_out' ? 'selected' : '' }}>Checked-out</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Room</label>
                <select name="room_id" class="form-control">
                    <option value="">All Rooms</option>
                    @foreach($rooms as $room)
                    <option value="{{ $room->id }}" {{ request('room_id') == $room->id ? 'selected' : '' }}>
                        {{ $room->room_number }} ({{ $room->type }})
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Customer Name</label>
                <input type="text" name="customer_name" class="form-control" 
                       value="{{ request('customer_name') }}" placeholder="Search by name...">
            </div>
            <div class="col-md-3">
                <label class="form-label">Check-in From</label>
                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Check-in To</label>
                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Sort By</label>
                <select name="order_by" class="form-control">
                    <option value="created_at" {{ request('order_by') == 'created_at' ? 'selected' : '' }}>Booking Date</option>
                    <option value="check_in_date" {{ request('order_by') == 'check_in_date' ? 'selected' : '' }}>Check-in Date</option>
                    <option value="check_out_date" {{ request('order_by') == 'check_out_date' ? 'selected' : '' }}>Check-out Date</option>
                    <option value="total_amount" {{ request('order_by') == 'total_amount' ? 'selected' : '' }}>Total Amount</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Order</label>
                <select name="order_dir" class="form-control">
                    <option value="desc" {{ request('order_dir') == 'desc' ? 'selected' : '' }}>Descending</option>
                    <option value="asc" {{ request('order_dir') == 'asc' ? 'selected' : '' }}>Ascending</option>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <div class="d-grid gap-2 w-100">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-filter"></i> Apply Filters
                    </button>
                </div>
            </div>
        </form>
        
        <div class="row mt-3">
            <div class="col-12">
                <div class="btn-group" role="group">
                    <a href="{{ route('admin.bookings.index') }}?upcoming=1" class="btn btn-outline-info">
                        <i class="bi bi-calendar-plus"></i> Upcoming
                    </a>
                    <a href="{{ route('admin.bookings.index') }}?current=1" class="btn btn-outline-success">
                        <i class="bi bi-house-check"></i> Current Stays
                    </a>
                    <a href="{{ route('admin.bookings.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle"></i> Clear Filters
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bookings Table -->
<div class="card border-0 shadow-sm">
    <div class="card-body">
        @if($bookings->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-calendar-x display-1 text-muted"></i>
                <h4 class="mt-3">No Bookings Found</h4>
                <p class="text-muted">Try adjusting your filters or create a new booking.</p>
                <a href="{{ route('admin.bookings.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Create New Booking
                </a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Booking ID</th>
                            <th>Customer</th>
                            <th>Room</th>
                            <th>Check-in</th>
                            <th>Check-out</th>
                            <th>Nights</th>
                            <th>Amount</th>
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
                                <strong>{{ $booking->customer_name }}</strong><br>
                                <small class="text-muted">{{ $booking->customer_email }}</small><br>
                                <small>{{ $booking->customer_phone }}</small>
                            </td>
                            <td>
                                {{ $booking->room->room_number }}<br>
                                <small class="text-muted">{{ $booking->room->type }}</small>
                            </td>
                            <td>
                                {{ $booking->check_in_date->format('M d, Y') }}<br>
                                <small class="text-muted">{{ $booking->check_in_date->diffForHumans() }}</small>
                            </td>
                            <td>
                                {{ $booking->check_out_date->format('M d, Y') }}
                            </td>
                            <td>{{ $booking->total_nights }}</td>
                            <td>
                                <strong>{{ $booking->formatted_total }}</strong>
                            </td>
                            <td>
                                <span class="badge bg-{{ $booking->statusBadge }} p-2">
                                    {{ ucfirst($booking->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.bookings.show', $booking) }}" class="btn btn-sm btn-outline-primary" title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.bookings.edit', $booking) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @if($booking->status == 'confirmed')
                                    <form action="{{ route('admin.bookings.check-in', $booking) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-success" title="Check-in">
                                            <i class="bi bi-door-open"></i>
                                        </button>
                                    </form>
                                    @endif
                                    @if($booking->status == 'checked_in')
                                    <form action="{{ route('admin.bookings.check-out', $booking) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-info" title="Check-out">
                                            <i class="bi bi-door-closed"></i>
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
            <div class="mt-4">
                {{ $bookings->links() }}
            </div>
            
            <div class="alert alert-info mt-3">
                <i class="bi bi-info-circle"></i>
                Showing {{ $bookings->firstItem() }} to {{ $bookings->lastItem() }} of {{ $bookings->total() }} bookings
            </div>
        @endif
    </div>
</div>
@endsection