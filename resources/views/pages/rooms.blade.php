@extends('layouts.app')

@section('title', 'Our Rooms - Royal Suites Hotel')

@section('content')
<div class="container py-5">
    <!-- Header -->
    <div class="row mb-5">
        <div class="col-lg-8 mx-auto text-center">
            <h1 class="display-4 fw-bold mb-3">Our Rooms & Suites</h1>
            <p class="lead text-muted">Experience luxury and comfort in our carefully designed accommodations</p>
        </div>
    </div>

    <!-- Search Filter -->
    <div class="card border-0 shadow-sm mb-5">
        <div class="card-body p-4">
            <form action="{{ route('rooms.index') }}" method="GET" id="roomFilter">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Check-in Date</label>
                        <input type="date" class="form-control" name="check_in" id="check_in">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Check-out Date</label>
                        <input type="date" class="form-control" name="check_out" id="check_out">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Room Type</label>
                        <select class="form-control" name="type" id="room_type">
                            <option value="">All Types</option>
                            <option value="Single">Single</option>
                            <option value="Double">Double</option>
                            <option value="Suite">Suite</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Guests</label>
                        <select class="form-control" name="capacity" id="capacity">
                            <option value="">Any</option>
                            <option value="1">1 Guest</option>
                            <option value="2">2 Guests</option>
                            <option value="3">3 Guests</option>
                            <option value="4">4+ Guests</option>
                        </select>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Room Grid -->
    <div class="row mb-5">
        @if($rooms->isEmpty())
            <div class="col-12">
                <div class="alert alert-info text-center py-5">
                    <i class="bi bi-door-closed display-1 text-muted mb-3"></i>
                    <h3>No Rooms Available</h3>
                    <p class="mb-0">Please adjust your search criteria or check back later.</p>
                </div>
            </div>
        @else
            @foreach($rooms as $room)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card room-card border-0 shadow-sm h-100">
                    <div class="room-image" style="
                        height: 250px;
                        background: #f8f9fa;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        color: #6c757d;
                        font-size: 4rem;
                        position: relative;
                    ">
                        @if($room->type == 'Suite')
                            🏨
                        @elseif($room->type == 'Double')
                            👥
                        @else
                            👤
                        @endif
                        @if(!$room->availability_status)
                        <div class="position-absolute top-0 start-0 m-3">
                            <span class="badge bg-danger">Unavailable</span>
                        </div>
                        @endif
                    </div>
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h5 class="card-title mb-1">{{ $room->room_number }} - {{ $room->type }}</h5>
                                <p class="text-muted mb-0">{{ Str::limit($room->description, 60) }}</p>
                            </div>
                            <span class="badge bg-{{ $room->type == 'Suite' ? 'warning' : ($room->type == 'Double' ? 'info' : 'primary') }}">
                                {{ $room->type }}
                            </span>
                        </div>
                        
                        <div class="mb-4">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-people text-muted me-2"></i>
                                <small class="text-muted">Up to {{ $room->capacity }} guest{{ $room->capacity > 1 ? 's' : '' }}</small>
                            </div>
                            @if($room->amenities)
                            <div class="mb-2">
                                <small class="text-muted"><i class="bi bi-star me-1"></i> {{ Str::limit($room->amenities, 70) }}</small>
                            </div>
                            @endif
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="text-primary mb-0">{{ $room->formatted_price }}</h4>
                                <small class="text-muted">per night</small>
                            </div>
                            <div>
                                <a href="{{ route('rooms.show', $room) }}" class="btn btn-outline-primary btn-sm me-2">
                                    <i class="bi bi-eye"></i> Details
                                </a>
                                @if($room->availability_status)
                                <a href="{{ route('bookings.create') }}?room={{ $room->id }}" class="btn btn-primary btn-sm">
                                    <i class="bi bi-calendar-check"></i> Book
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        @endif
    </div>

    <!-- Room Types Comparison -->
    <div class="row mb-5">
        <div class="col-12">
            <h2 class="fw-bold text-center mb-4">Room Types Comparison</h2>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Features</th>
                            <th class="text-center">Single Room</th>
                            <th class="text-center">Double Room</th>
                            <th class="text-center">Suite</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Average Size</td>
                            <td class="text-center">25 m²</td>
                            <td class="text-center">35 m²</td>
                            <td class="text-center">50 m²</td>
                        </tr>
                        <tr>
                            <td>Max Guests</td>
                            <td class="text-center">1</td>
                            <td class="text-center">2</td>
                            <td class="text-center">4</td>
                        </tr>
                        <tr>
                            <td>King Bed</td>
                            <td class="text-center"><i class="bi bi-x text-danger"></i></td>
                            <td class="text-center"><i class="bi bi-check text-success"></i></td>
                            <td class="text-center"><i class="bi bi-check text-success"></i></td>
                        </tr>
                        <tr>
                            <td>Living Area</td>
                            <td class="text-center"><i class="bi bi-x text-danger"></i></td>
                            <td class="text-center"><i class="bi bi-check text-success"></i></td>
                            <td class="text-center"><i class="bi bi-check text-success"></i></td>
                        </tr>
                        <tr>
                            <td>Kitchenette</td>
                            <td class="text-center"><i class="bi bi-x text-danger"></i></td>
                            <td class="text-center"><i class="bi bi-x text-danger"></i></td>
                            <td class="text-center"><i class="bi bi-check text-success"></i></td>
                        </tr>
                        <tr>
                            <td>Balcony</td>
                            <td class="text-center"><i class="bi bi-check text-success"></i></td>
                            <td class="text-center"><i class="bi bi-check text-success"></i></td>
                            <td class="text-center"><i class="bi bi-check text-success"></i></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- CTA -->
    <div class="text-center">
        <div class="card bg-primary text-white border-0 overflow-hidden">
            <div class="card-body p-5">
                <h2 class="fw-bold mb-3">Need Help Choosing?</h2>
                <p class="mb-4">Our team is ready to help you find the perfect room for your stay.</p>
                <div class="d-flex justify-content-center gap-3">
                    <a href="{{ route('bookings.create') }}" class="btn btn-light btn-lg">
                        <i class="bi bi-calendar-check me-2"></i> Book Now
                    </a>
                    <a href="{{ route('contact') }}" class="btn btn-outline-light btn-lg">
                        <i class="bi bi-telephone me-2"></i> Contact Us
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .room-card {
        transition: transform 0.3s, box-shadow 0.3s;
        border-radius: 10px;
    }
    
    .room-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
    }
    
    .room-image {
        border-top-left-radius: 10px;
        border-top-right-radius: 10px;
    }
</style>
@endsection

@section('scripts')
<script>
    // Set default dates for room search
    document.addEventListener('DOMContentLoaded', function() {
        const today = new Date().toISOString().split('T')[0];
        const checkInInput = document.getElementById('check_in');
        const checkOutInput = document.getElementById('check_out');
        
        if (checkInInput) {
            checkInInput.min = today;
            checkInInput.value = today;
            
            // Set check-out to tomorrow
            const tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            const tomorrowStr = tomorrow.toISOString().split('T')[0];
            
            if (checkOutInput) {
                checkOutInput.min = tomorrowStr;
                checkOutInput.value = tomorrowStr;
            }
            
            // Update check-out min date when check-in changes
            checkInInput.addEventListener('change', function() {
                if (checkOutInput) {
                    const nextDay = new Date(this.value);
                    nextDay.setDate(nextDay.getDate() + 1);
                    checkOutInput.min = nextDay.toISOString().split('T')[0];
                    
                    // If current check-out is before new min, update it
                    if (new Date(checkOutInput.value) < nextDay) {
                        checkOutInput.value = checkOutInput.min;
                    }
                }
            });
        }
        
        // Auto-submit filter form on change
        const filterForm = document.getElementById('roomFilter');
        const filterInputs = filterForm.querySelectorAll('select, input[type="date"]');
        
        filterInputs.forEach(input => {
            input.addEventListener('change', function() {
                filterForm.submit();
            });
        });
    });
</script>
@endsection