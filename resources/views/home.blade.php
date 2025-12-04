@extends('layouts.app')

@section('title', 'Welcome to Royal Suites Hotel')

@section('content')
<!-- Hero Section -->
<section class="hero-section" style="
    background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), 
                url('/images/photo-1566073771259-6a8506099945.jpg');
    background-position: center;
    background-size: cover;
    color: white;
    padding: 120px 0;
    margin-bottom: 60px;
">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-4">Experience Luxury & Comfort</h1>
                <p class="lead mb-4">Book your perfect stay at Royal Suites Hotel. Enjoy premium amenities, exceptional service, and unforgettable memories.</p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="{{ route('bookings.create') }}" class="btn btn-primary btn-lg px-4 py-3">
                        <i class="bi bi-calendar-check me-2"></i> Book Now
                    </a>
                    <a href="#rooms" class="btn btn-outline-light btn-lg px-4 py-3">
                        <i class="bi bi-door-closed me-2"></i> View Rooms
                    </a>
                </div>
            </div>
            <div class="col-lg-6 mt-4 mt-lg-0">
                <!-- Quick Booking Form -->
                <div class="card bg-dark bg-opacity-75 border-light">
                    <div class="card-body p-4">
                        <h4 class="mb-4"><i class="bi bi-search me-2"></i> Find Your Room</h4>
                        <form action="{{ route('rooms.index') }}" method="GET">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Check-in Date</label>
                                    <input type="date" class="form-control" name="check_in" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Check-out Date</label>
                                    <input type="date" class="form-control" name="check_out" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Room Type</label>
                                    <select class="form-control" name="type">
                                        <option value="">All Types</option>
                                        @foreach($roomTypes as $type)
                                            <option value="{{ $type }}">{{ $type }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Guests</label>
                                    <select class="form-control" name="guests">
                                        <option value="1">1 Guest</option>
                                        <option value="2">2 Guests</option>
                                        <option value="3">3 Guests</option>
                                        <option value="4">4 Guests</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary w-100 py-3">
                                        <i class="bi bi-search me-2"></i> Check Availability
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="stats-section mb-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-3">
                <div class="text-center p-4 border rounded shadow-sm">
                    <i class="bi bi-door-closed display-4 text-primary mb-3"></i>
                    <h3 class="fw-bold">{{ $totalRooms }}</h3>
                    <p class="text-muted mb-0">Total Rooms</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="text-center p-4 border rounded shadow-sm">
                    <i class="bi bi-check-circle display-4 text-success mb-3"></i>
                    <h3 class="fw-bold">{{ $availableRooms }}</h3>
                    <p class="text-muted mb-0">Available Now</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="text-center p-4 border rounded shadow-sm">
                    <i class="bi bi-people display-4 text-info mb-3"></i>
                    <h3 class="fw-bold">{{ $totalBookings }}+</h3>
                    <p class="text-muted mb-0">Happy Guests</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="text-center p-4 border rounded shadow-sm">
                    <i class="bi bi-star display-4 text-warning mb-3"></i>
                    <h3 class="fw-bold">4.8/5</h3>
                    <p class="text-muted mb-0">Guest Rating</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Rooms Section -->
<section id="rooms" class="featured-rooms mb-5">
    <div class="container">
        <div class="row mb-5">
            <div class="col-md-8">
                <h2 class="fw-bold">Our Featured Rooms</h2>
                <p class="text-muted">Experience comfort and luxury in our carefully designed rooms</p>
            </div>
            <div class="col-md-4 text-end">
                <a href="{{ route('rooms.index') }}" class="btn btn-outline-primary">
                    View All Rooms <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
        
        <div class="row g-4">
            @foreach($featuredRooms as $room)
            <div class="col-md-4">
                <div class="card room-card shadow-sm border-0 overflow-hidden">
                    <div class="room-image" style="
                        height: 250px;
                        background: #f8f9fa;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        color: #6c757d;
                        font-size: 4rem;
                    ">
                        @if($room->type == 'Suite')
                            🏨
                        @elseif($room->type == 'Double')
                            👥
                        @else
                            👤
                        @endif
                    </div>
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h5 class="card-title mb-1">{{ $room->room_number }} - {{ $room->type }}</h5>
                                <p class="text-muted mb-0">{{ $room->description }}</p>
                            </div>
                            <span class="badge bg-success">Available</span>
                        </div>
                        
                        <div class="mb-3">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-people text-muted me-2"></i>
                                <small>Capacity: {{ $room->capacity }} person{{ $room->capacity > 1 ? 's' : '' }}</small>
                            </div>
                            @if($room->amenities)
                            <div class="mb-2">
                                <small class="text-muted">{{ Str::limit($room->amenities, 50) }}</small>
                            </div>
                            @endif
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="text-primary mb-0">{{ $room->formatted_price }}</h4>
                                <small class="text-muted">per night</small>
                            </div>
                            <div>
                                <a href="{{ route('rooms.show', $room) }}" class="btn btn-sm btn-outline-primary me-2">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('bookings.create') }}?room={{ $room->id }}" class="btn btn-sm btn-primary">
                                    Book Now
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Amenities Section -->
<section class="amenities-section mb-5 py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Our Amenities</h2>
            <p class="text-muted">Everything you need for a comfortable stay</p>
        </div>
        
        <div class="row g-4">
            @foreach(array_chunk($amenities, 4) as $amenityGroup)
            <div class="col-md-6">
                <div class="card border-0 bg-white shadow-sm">
                    <div class="card-body p-4">
                        <ul class="list-unstyled mb-0">
                            @foreach($amenityGroup as $amenity)
                            <li class="mb-3 d-flex align-items-center">
                                <i class="bi bi-check-circle-fill text-success me-3"></i>
                                <span class="h5 mb-0">{{ $amenity }}</span>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section class="testimonials-section mb-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">What Our Guests Say</h2>
            <p class="text-muted">Don't just take our word for it</p>
        </div>
        
        <div class="row g-4">
            @foreach($testimonials as $testimonial)
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="mb-4">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= $testimonial['rating'])
                                    <i class="bi bi-star-fill text-warning"></i>
                                @else
                                    <i class="bi bi-star text-warning"></i>
                                @endif
                            @endfor
                        </div>
                        
                        <p class="card-text mb-4">"{{ $testimonial['content'] }}"</p>
                        
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                    {{ substr($testimonial['name'], 0, 1) }}
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-0">{{ $testimonial['name'] }}</h6>
                                <small class="text-muted">{{ $testimonial['role'] }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section mb-5">
    <div class="container">
        <div class="card bg-primary text-white border-0 overflow-hidden">
            <div class="row g-0 align-items-center">
                <div class="col-lg-8 p-5">
                    <h2 class="fw-bold mb-3">Ready to Book Your Stay?</h2>
                    <p class="mb-4">Join thousands of satisfied guests who have experienced our premium hospitality.</p>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="{{ route('bookings.create') }}" class="btn btn-light btn-lg px-4">
                            <i class="bi bi-calendar-check me-2"></i> Book Now
                        </a>
                        @if(!Auth::guard('customer')->check())
                            <a href="{{ route('customer.register') }}" class="btn btn-outline-light btn-lg px-4">
                                <i class="bi bi-person-plus me-2"></i> Create Account
                            </a>
                        @endif
                    </div>
                </div>
                <div class="col-lg-4 d-none d-lg-block">
                    <div class="text-center p-5">
                        <i class="bi bi-building display-1 opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="faq-section mb-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Frequently Asked Questions</h2>
            <p class="text-muted">Find answers to common questions</p>
        </div>
        
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="accordion" id="faqAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                What time is check-in and check-out?
                            </button>
                        </h2>
                        <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Check-in time is 3:00 PM and check-out time is 11:00 AM. Early check-in and late check-out may be available upon request.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                Is breakfast included?
                            </button>
                        </h2>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Yes, a complimentary breakfast buffet is included with all room bookings from 7:00 AM to 10:30 AM.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                What is your cancellation policy?
                            </button>
                        </h2>
                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                You can cancel your booking free of charge up to 48 hours before check-in. Cancellations within 48 hours may incur a one-night charge.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                Do you have parking available?
                            </button>
                        </h2>
                        <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Yes, we offer complimentary self-parking for all hotel guests. Valet parking is also available for an additional fee.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('styles')
<style>
    .hero-section {
        border-radius: 15px;
        margin-top: 20px;
    }
    
    .room-card {
        transition: transform 0.3s, box-shadow 0.3s;
        border-radius: 10px;
    }
    
    .room-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
    }
    
    .room-image {
        background-size: cover;
        background-position: center;
    }
    
    .stats-section .col-md-3 > div {
        transition: transform 0.3s;
    }
    
    .stats-section .col-md-3 > div:hover {
        transform: translateY(-5px);
    }
    
    .accordion-button:not(.collapsed) {
        background-color: rgba(52, 152, 219, 0.1);
        color: #3498db;
    }
</style>
@endsection

@section('scripts')
<script>
    // Set minimum date for check-in to today
    document.addEventListener('DOMContentLoaded', function() {
        const today = new Date().toISOString().split('T')[0];
        const checkInInput = document.querySelector('input[name="check_in"]');
        const checkOutInput = document.querySelector('input[name="check_out"]');
        
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
    });
</script>
@endsection