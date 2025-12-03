@extends('layouts.app')

@section('title', 'All Rooms')

@section('content')
<div class="row mb-4">
    <div class="col-md-6">
        <h1><i class="bi bi-door-closed"></i> Room Management</h1>
        <p class="text-muted">Manage all rooms in the system</p>
    </div>
    <div class="col-md-6 text-end">
        <a href="{{ route('rooms.create') }}" class="btn btn-success">
            <i class="bi bi-plus-circle"></i> Add New Room
        </a>
        <a href="{{ route('bookings.create') }}" class="btn btn-primary">
            <i class="bi bi-calendar-check"></i> New Booking
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <i class="bi bi-list"></i> All Rooms
    </div>
    <div class="card-body">
        @if($rooms->isEmpty())
            <div class="alert alert-info">
                No rooms found. <a href="{{ route('rooms.create') }}">Add your first room</a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Room #</th>
                            <th>Type</th>
                            <th>Price/Night</th>
                            <th>Capacity</th>
                            <th>Amenities</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rooms as $room)
                        <tr>
                            <td>
                                <strong>{{ $room->room_number }}</strong>
                                @if($room->description)
                                    <br><small class="text-muted">{{ Str::limit($room->description, 50) }}</small>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-info">
                                    {{ $room->type }}
                                </span>
                            </td>
                            <td>
                                <span class="text-success fw-bold">{{ $room->formatted_price }}</span>
                            </td>
                            <td>
                                <span class="badge bg-secondary">
                                    {{ $room->capacity }} person{{ $room->capacity > 1 ? 's' : '' }}
                                </span>
                            </td>
                            <td>
                                @if($room->amenities)
                                    <small>{{ Str::limit($room->amenities, 60) }}</small>
                                @else
                                    <span class="text-muted">None</span>
                                @endif
                            </td>
                            <td>
                                @if($room->availability_status)
                                    <span class="status-available">
                                        <i class="bi bi-check-circle"></i> Available
                                    </span>
                                @else
                                    <span class="status-unavailable">
                                        <i class="bi bi-x-circle"></i> Unavailable
                                    </span>
                                @endif
                            </td>
                            <td class="action-buttons">
                                <a href="{{ route('rooms.show', $room) }}" class="btn btn-sm btn-info" title="View">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('rooms.edit', $room) }}" class="btn btn-sm btn-warning" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                
                                <form action="{{ route('rooms.toggle-availability', $room) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm {{ $room->availability_status ? 'btn-secondary' : 'btn-success' }}" title="Toggle Availability">
                                        <i class="bi bi-power"></i>
                                    </button>
                                </form>
                                
                                <form action="{{ route('rooms.destroy', $room) }}" method="POST" class="d-inline" id="delete-form-{{ $room->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" onclick="confirmDelete('delete-form-{{ $room->id }}')" class="btn btn-sm btn-danger" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="row mt-3">
                <div class="col-md-12">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i>
                        <strong>Total Rooms:</strong> {{ $rooms->count() }} | 
                        <strong>Available:</strong> {{ $rooms->where('availability_status', true)->count() }} | 
                        <strong>Unavailable:</strong> {{ $rooms->where('availability_status', false)->count() }}
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection