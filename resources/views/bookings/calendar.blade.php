@extends('admin.layouts.app')

@section('title', 'Booking Calendar')

@section('content')
<div class="row mb-4">
    <div class="col-md-6">
        <h1><i class="bi bi-calendar-week"></i> Booking Calendar</h1>
        <p class="text-muted">View bookings in calendar format</p>
    </div>
    <div class="col-md-6 text-end">
        <a href="{{ route('admin.bookings.index') }}" class="btn btn-secondary">
            <i class="bi bi-list"></i> List View
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Room</th>
                        @for($i = 0; $i < 30; $i++)
                            <th class="text-center">{{ \Carbon\Carbon::today()->addDays($i)->format('M d') }}</th>
                        @endfor
                    </tr>
                </thead>
                <tbody>
                    @foreach($rooms as $room)
                    <tr>
                        <td>
                            <strong>{{ $room->room_number }}</strong><br>
                            <small class="text-muted">{{ $room->type }}</small>
                        </td>
                        @for($i = 0; $i < 30; $i++)
                            @php
                                $date = \Carbon\Carbon::today()->addDays($i);
                                $booking = $bookings->first(function($booking) use ($room, $date) {
                                    return $booking->room_id == $room->id && 
                                           $date->between($booking->check_in_date, $booking->check_out_date->subDay());
                                });
                            @endphp
                            <td class="text-center">
                                @if($booking)
                                    @php
                                        $statusColors = [
                                            'confirmed' => 'primary',
                                            'checked_in' => 'success',
                                            'checked_out' => 'secondary',
                                            'cancelled' => 'danger',
                                            'pending' => 'warning'
                                        ];
                                    @endphp
                                    <span class="badge bg-{{ $statusColors[$booking->status] ?? 'primary' }}" 
                                          title="{{ $booking->customer_name }} ({{ $booking->status }})">
                                        ●
                                    </span>
                                @else
                                    @if($room->availability_status)
                                        <span class="text-success" title="Available">○</span>
                                    @else
                                        <span class="text-danger" title="Unavailable">○</span>
                                    @endif
                                @endif
                            </td>
                        @endfor
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="mt-4">
            <h6>Legend:</h6>
            <div class="d-flex flex-wrap gap-3">
                <span class="badge bg-primary">● Confirmed</span>
                <span class="badge bg-success">● Checked-in</span>
                <span class="badge bg-secondary">● Checked-out</span>
                <span class="badge bg-danger">● Cancelled</span>
                <span class="badge bg-warning">● Pending</span>
                <span class="text-success">○ Available</span>
                <span class="text-danger">○ Unavailable</span>
            </div>
        </div>
    </div>
</div>
@endsection