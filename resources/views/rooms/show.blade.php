@extends('layouts.app')

@section('title', 'Room Details')

@section('content')
<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header bg-info text-white">
                <i class="bi bi-door-open"></i> Room Details: {{ $room->room_number }}
            </div>
            <!-- In the room details section, add this -->
<div class="text-center mt-4">
    @if($room->availability_status)
    <a href="{{ route('bookings.create') }}?room={{ $room->id }}" class="btn btn-primary btn-lg">
        <i class="bi bi-calendar-check me-2"></i> Book This Room Now
    </a>
    @else
    <button class="btn btn-secondary btn-lg" disabled>
        <i class="bi bi-x-circle me-2"></i> Currently Unavailable
    </button>
    @endif
</div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h4>{{ $room->room_number }} - {{ $room->type }}</h4>
                        <p class="text-muted">{{ $room->description }}</p>
                        
                        <div class="mb-3">
                            <span class="badge bg-primary fs-6 p-2">{{ $room->formatted_price }} / night</span>
                            <span class="badge bg-secondary fs-6 p-2">{{ $room->capacity }} person{{ $room->capacity > 1 ? 's' : '' }}</span>
                            <span class="badge {{ $room->availability_status ? 'bg-success' : 'bg-danger' }} fs-6 p-2">
                                {{ $room->availability_status ? 'Available' : 'Unavailable' }}
                            </span>
                        </div>
                        
                        @if($room->amenities)
                        <div class="mb-3">
                            <h6><i class="bi bi-star"></i> Amenities:</h6>
                            <p>{{ $room->amenities }}</p>
                        </div>
                        @endif
                    </div>
                    
                    <div class="col-md-6 text-end">
                        <div class="btn-group" role="group">
                            <a href="{{ route('rooms.edit', $room) }}" class="btn btn-warning">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                            <form action="{{ route('rooms.toggle-availability', $room) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn {{ $room->availability_status ? 'btn-secondary' : 'btn-success' }}">
                                    <i class="bi bi-power"></i> {{ $room->availability_status ? 'Mark Unavailable' : 'Mark Available' }}
                                </button>
                            </form>
                            <a href="{{ route('bookings.create') }}?room={{ $room->id }}" class="btn btn-primary">
                                <i class="bi bi-calendar-check"></i> Book This Room
                            </a>
                        </div>
                    </div>
                </div>
                
                <hr>
                
                <h5><i class="bi bi-calendar"></i> Recent Bookings</h5>
                @if($bookings->isEmpty())
                    <div class="alert alert-info">
                        No bookings for this room yet.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Customer</th>
                                    <th>Check-in</th>
                                    <th>Check-out</th>
                                    <th>Nights</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bookings as $booking)
                                <tr>
                                    <td>
                                        <strong>{{ $booking->customer_name }}</strong><br>
                                        <small>{{ $booking->customer_email }}</small>
                                    </td>
                                    <td>{{ $booking->check_in_date->format('M d, Y') }}</td>
                                    <td>{{ $booking->check_out_date->format('M d, Y') }}</td>
                                    <td>{{ $booking->total_nights }}</td>
                                    <td>{{ $booking->formatted_total }}</td>
                                    <td>
                                        <span class="badge bg-{{ $booking->statusBadge }}">
                                            {{ ucfirst($booking->status) }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
                
                <div class="mt-3">
                    <a href="{{ route('rooms.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Back to All Rooms
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection