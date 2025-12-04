@extends('layouts.app')

@section('title', 'About Us - Royal Suites Hotel')

@section('content')
<div class="container py-5">
    <!-- Hero Section -->
    <div class="row mb-5">
        <div class="col-lg-8 mx-auto text-center">
            <h1 class="display-4 fw-bold mb-4">About Royal Suites Hotel</h1>
            <p class="lead text-muted">Experience luxury, comfort, and exceptional service at our premier hotel</p>
        </div>
    </div>

    <!-- Story Section -->
    <div class="row align-items-center mb-5">
        <div class="col-lg-6">
            <h2 class="fw-bold mb-4">Our Story</h2>
            <p class="mb-3">Founded in 2010, Royal Suites Hotel began with a simple vision: to create a sanctuary where every guest feels valued and every stay is memorable.</p>
            <p class="mb-3">Over the years, we've grown from a small boutique hotel to a premier destination, known for our attention to detail and commitment to excellence.</p>
            <p>Today, we continue to uphold our founding principles while embracing innovation and sustainability in hospitality.</p>
        </div>
        <div class="col-lg-6">
            <div class="card border-0 shadow-lg">
                <div class="card-body p-5 text-center">
                    <i class="bi bi-building display-1 text-primary mb-4"></i>
                    <h4>Over a Decade of Excellence</h4>
                    <p class="text-muted">Serving guests with passion since 2010</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Mission & Vision -->
    <div class="row mb-5">
        <div class="col-md-6 mb-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <i class="bi bi-bullseye display-4 text-primary"></i>
                    </div>
                    <h3 class="text-center mb-3">Our Mission</h3>
                    <p class="text-center">To provide exceptional hospitality experiences that exceed guest expectations through personalized service, luxurious accommodations, and attention to every detail.</p>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <i class="bi bi-eye display-4 text-success"></i>
                    </div>
                    <h3 class="text-center mb-3">Our Vision</h3>
                    <p class="text-center">To be recognized as the leading hotel brand in the region, known for innovative hospitality solutions and sustainable practices that create lasting value for our guests and community.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Values -->
    <div class="row mb-5">
        <div class="col-12 text-center mb-4">
            <h2 class="fw-bold">Our Core Values</h2>
            <p class="text-muted">The principles that guide everything we do</p>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <i class="bi bi-heart display-4 text-danger mb-3"></i>
                    <h5>Passion</h5>
                    <p class="text-muted small">We love what we do and it shows in every interaction</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <i class="bi bi-shield-check display-4 text-primary mb-3"></i>
                    <h5>Integrity</h5>
                    <p class="text-muted small">We do the right thing, even when no one is watching</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <i class="bi bi-people display-4 text-success mb-3"></i>
                    <h5>Teamwork</h5>
                    <p class="text-muted small">We work together to achieve extraordinary results</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <i class="bi bi-star display-4 text-warning mb-3"></i>
                    <h5>Excellence</h5>
                    <p class="text-muted small">We strive for perfection in everything we do</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Team -->
    <div class="row mb-5">
        <div class="col-12 text-center mb-4">
            <h2 class="fw-bold">Meet Our Leadership Team</h2>
            <p class="text-muted">The dedicated professionals behind our success</p>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body p-4">
                    <div class="mx-auto mb-3" style="width: 100px; height: 100px; background: #f8f9fa; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <span class="h3">JD</span>
                    </div>
                    <h5 class="mb-1">John Davis</h5>
                    <p class="text-muted small">General Manager</p>
                    <p class="small">20+ years in hospitality management</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body p-4">
                    <div class="mx-auto mb-3" style="width: 100px; height: 100px; background: #f8f9fa; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <span class="h3">SR</span>
                    </div>
                    <h5 class="mb-1">Sarah Roberts</h5>
                    <p class="text-muted small">Operations Director</p>
                    <p class="small">Expert in guest experience optimization</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body p-4">
                    <div class="mx-auto mb-3" style="width: 100px; height: 100px; background: #f8f9fa; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <span class="h3">MC</span>
                    </div>
                    <h5 class="mb-1">Michael Chen</h5>
                    <p class="text-muted small">Head Chef</p>
                    <p class="small">Award-winning culinary expert</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Awards & Recognition -->
    <div class="card bg-light border-0 mb-5">
        <div class="card-body p-5">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h3 class="fw-bold mb-3">Awards & Recognition</h3>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="bi bi-award text-warning me-2"></i> TripAdvisor Travelers' Choice 2023</li>
                        <li class="mb-2"><i class="bi bi-award text-warning me-2"></i> Luxury Hotel Awards - Best Service 2022</li>
                        <li class="mb-2"><i class="bi bi-award text-warning me-2"></i> Sustainable Tourism Certification 2021</li>
                        <li><i class="bi bi-award text-warning me-2"></i> Hospitality Excellence Award 2020</li>
                    </ul>
                </div>
                <div class="col-md-4 text-center">
                    <i class="bi bi-trophy display-1 text-warning opacity-25"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- CTA -->
    <div class="text-center">
        <h3 class="fw-bold mb-4">Experience Royal Suites Hotel</h3>
        <p class="mb-4">Join our family of satisfied guests and experience the difference</p>
        <div class="d-flex justify-content-center gap-3">
            <a href="{{ route('bookings.create') }}" class="btn btn-primary btn-lg">
                <i class="bi bi-calendar-check me-2"></i> Book Your Stay
            </a>
            <a href="{{ route('rooms.index') }}" class="btn btn-outline-primary btn-lg">
                <i class="bi bi-door-closed me-2"></i> View Rooms
            </a>
        </div>
    </div>
</div>
@endsection