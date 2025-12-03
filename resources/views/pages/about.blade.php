@extends('layouts.app')

@section('title', 'Contact Us - Royal Suites Hotel')

@section('content')
<div class="container py-5">
    <!-- Header -->
    <div class="row mb-5">
        <div class="col-lg-8 mx-auto text-center">
            <h1 class="display-4 fw-bold mb-3">Contact Us</h1>
            <p class="lead text-muted">We're here to help. Get in touch with us for any inquiries or assistance.</p>
        </div>
    </div>

    <div class="row">
        <!-- Contact Form -->
        <div class="col-lg-7 mb-5">
            <div class="card border-0 shadow-lg">
                <div class="card-body p-5">
                    <h3 class="fw-bold mb-4">Send us a Message</h3>
                    
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    <form id="contactForm">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Full Name *</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email Address *</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="subject" class="form-label">Subject *</label>
                            <input type="text" class="form-control" id="subject" name="subject" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="message" class="form-label">Message *</label>
                            <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="newsletter">
                                <label class="form-check-label" for="newsletter">
                                    Subscribe to our newsletter for updates and offers
                                </label>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-lg w-100">
                            <i class="bi bi-send me-2"></i> Send Message
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="col-lg-5">
            <!-- Contact Info Cards -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="d-flex">
                        <div class="flex-shrink-0">
                            <i class="bi bi-geo-alt display-6 text-primary"></i>
                        </div>
                        <div class="flex-grow-1 ms-4">
                            <h5 class="fw-bold">Our Location</h5>
                            <p class="mb-0">123 Luxury Avenue</p>
                            <p class="mb-0">New York, NY 10001</p>
                            <p class="mb-0">United States</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="d-flex">
                        <div class="flex-shrink-0">
                            <i class="bi bi-telephone display-6 text-success"></i>
                        </div>
                        <div class="flex-grow-1 ms-4">
                            <h5 class="fw-bold">Phone Numbers</h5>
                            <p class="mb-2"><strong>Reservations:</strong> +1 (555) 123-4567</p>
                            <p class="mb-0"><strong>General Inquiries:</strong> +1 (555) 987-6543</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="d-flex">
                        <div class="flex-shrink-0">
                            <i class="bi bi-envelope display-6 text-info"></i>
                        </div>
                        <div class="flex-grow-1 ms-4">
                            <h5 class="fw-bold">Email Addresses</h5>
                            <p class="mb-2"><strong>Reservations:</strong> reservations@royalsuites.com</p>
                            <p class="mb-2"><strong>General:</strong> info@royalsuites.com</p>
                            <p class="mb-0"><strong>Support:</strong> support@royalsuites.com</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="d-flex">
                        <div class="flex-shrink-0">
                            <i class="bi bi-clock display-6 text-warning"></i>
                        </div>
                        <div class="flex-grow-1 ms-4">
                            <h5 class="fw-bold">Business Hours</h5>
                            <p class="mb-1"><strong>Front Desk:</strong> 24/7</p>
                            <p class="mb-1"><strong>Reservations:</strong> 8:00 AM - 10:00 PM EST</p>
                            <p class="mb-0"><strong>Restaurant:</strong> 7:00 AM - 11:00 PM</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Social Media -->
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Connect With Us</h5>
                    <div class="d-flex gap-3">
                        <a href="#" class="btn btn-outline-primary btn-lg">
                            <i class="bi bi-facebook"></i>
                        </a>
                        <a href="#" class="btn btn-outline-info btn-lg">
                            <i class="bi bi-twitter"></i>
                        </a>
                        <a href="#" class="btn btn-outline-danger btn-lg">
                            <i class="bi bi-instagram"></i>
                        </a>
                        <a href="#" class="btn btn-outline-dark btn-lg">
                            <i class="bi bi-linkedin"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Map -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="card border-0 shadow-lg">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Find Us on Map</h5>
                    <div class="border rounded p-4 text-center bg-light" style="height: 300px;">
                        <div class="d-flex align-items-center justify-content-center h-100">
                            <div class="text-center">
                                <i class="bi bi-map display-1 text-muted mb-3"></i>
                                <h5>Location Map</h5>
                                <p class="text-muted mb-0">123 Luxury Avenue, New York, NY 10001</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- FAQ Contact -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-5">
                    <h3 class="fw-bold mb-4">Common Contact Questions</h3>
                    <div class="accordion" id="contactFAQ">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                    How can I modify or cancel my booking?
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse show">
                                <div class="accordion-body">
                                    You can modify or cancel your booking by logging into your account or contacting our reservations team at +1 (555) 123-4567. Please refer to our cancellation policy for details.
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                    Do you offer airport transfer services?
                                </button>
                            </h2>
                            <div id="faq2" class="accordion-collapse collapse">
                                <div class="accordion-body">
                                    Yes, we offer airport transfer services. Please contact our concierge at least 24 hours before your arrival to arrange transportation.
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                    Are pets allowed in the hotel?
                                </button>
                            </h2>
                            <div id="faq3" class="accordion-collapse collapse">
                                <div class="accordion-body">
                                    We welcome pets in designated pet-friendly rooms. Please inform us in advance and review our pet policy for additional information.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.getElementById('contactForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Simple form validation
        const name = document.getElementById('name').value.trim();
        const email = document.getElementById('email').value.trim();
        const subject = document.getElementById('subject').value.trim();
        const message = document.getElementById('message').value.trim();
        
        if (!name || !email || !subject || !message) {
            alert('Please fill in all required fields.');
            return;
        }
        
        // Simulate form submission
        alert('Thank you for your message! We will get back to you soon.');
        this.reset();
    });
</script>
@endsection