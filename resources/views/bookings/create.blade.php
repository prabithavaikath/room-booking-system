@extends('layouts.app')

@section('title', 'Book a Room - Royal Suites Hotel')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Booking Steps -->
            <div class="card border-0 shadow-sm mb-5">
                <div class="card-body p-4">
                    <div class="row text-center">
                        <div class="col-md-4">
                            <div class="step active">
                                <div class="step-number">1</div>
                                <h6 class="mt-2">Select Room & Dates</h6>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="step">
                                <div class="step-number">2</div>
                                <h6 class="mt-2">Guest Details</h6>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="step">
                                <div class="step-number">3</div>
                                <h6 class="mt-2">Confirmation</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Left Column - Room Selection & Dates -->
                <div class="col-lg-7 mb-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <h4 class="fw-bold mb-4">1. Select Room & Dates</h4>
                            
                            <!-- Room Selection -->
                            <div class="mb-4">
                                <label class="form-label fw-bold">Select a Room</label>
                                <div class="row g-3" id="room-selection">
                                    @foreach($rooms as $roomItem)
                                    <div class="col-md-6">
                                        <div class="card room-option {{ $room && $room->id == $roomItem->id ? 'selected' : '' }}" 
                                             data-room-id="{{ $roomItem->id }}"
                                             data-room-price="{{ $roomItem->price }}">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <div>
                                                        <h6 class="mb-1">{{ $roomItem->room_number }} - {{ $roomItem->type }}</h6>
                                                        <small class="text-muted">{{ Str::limit($roomItem->description, 40) }}</small>
                                                    </div>
                                                    <span class="badge bg-{{ $roomItem->type == 'Suite' ? 'warning' : ($roomItem->type == 'Double' ? 'info' : 'primary') }}">
                                                        {{ $roomItem->type }}
                                                    </span>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <h5 class="text-primary mb-0">{{ $roomItem->formatted_price }}</h5>
                                                        <small class="text-muted">per night</small>
                                                    </div>
                                                    <div>
                                                        <small><i class="bi bi-people"></i> {{ $roomItem->capacity }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            
                            <!-- Date Selection -->
                            <div class="mb-4">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="check_in_date" class="form-label fw-bold">Check-in Date</label>
                                        <input type="date" class="form-control" id="check_in_date" name="check_in_date" 
                                               value="{{ $defaultCheckIn }}" min="{{ date('Y-m-d') }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="check_out_date" class="form-label fw-bold">Check-out Date</label>
                                        <input type="date" class="form-control" id="check_out_date" name="check_out_date" 
                                               value="{{ $defaultCheckOut }}">
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Check Availability Button -->
                            <div class="d-grid mb-4">
                                <button type="button" id="check-availability" class="btn btn-primary btn-lg">
                                    <i class="bi bi-search me-2"></i> Check Availability & Price
                                </button>
                            </div>
                            
                            <!-- Availability Results -->
                            <div id="availability-results" class="d-none">
                                <div class="alert alert-success">
                                    <h5><i class="bi bi-check-circle me-2"></i> Room Available!</h5>
                                    <div class="row mt-3">
                                        <div class="col-md-6">
                                            <p class="mb-1"><strong>Selected Room:</strong> <span id="selected-room"></span></p>
                                            <p class="mb-1"><strong>Check-in:</strong> <span id="selected-checkin"></span></p>
                                            <p class="mb-1"><strong>Check-out:</strong> <span id="selected-checkout"></span></p>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="mb-1"><strong>Nights:</strong> <span id="selected-nights"></span></p>
                                            <p class="mb-1"><strong>Price per night:</strong> <span id="selected-price"></span></p>
                                            <p class="mb-0"><strong>Total amount:</strong> <span id="selected-total" class="fw-bold text-success"></span></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Right Column - Guest Details & Booking Form -->
                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm sticky-top" style="top: 20px;">
                        <div class="card-body p-4">
                            <h4 class="fw-bold mb-4">2. Guest Details</h4>
                            
                            @if(Auth::guard('customer')->check())
                            <div class="alert alert-info mb-4">
                                <i class="bi bi-info-circle me-2"></i>
                                You're logged in as <strong>{{ $customer->full_name }}</strong>. Your details will be auto-filled.
                            </div>
                            @endif
                            
                            <form id="booking-form" action="{{ route('bookings.store') }}" method="POST">
                                @csrf
                                
                                <!-- Hidden fields -->
                                <input type="hidden" id="room_id" name="room_id" value="{{ $room ? $room->id : '' }}">
                                <input type="hidden" id="final_check_in_date" name="check_in_date">
                                <input type="hidden" id="final_check_out_date" name="check_out_date">
                                
                                <!-- Guest Details -->
                                <div class="mb-3">
                                    <label for="customer_name" class="form-label">Full Name *</label>
                                    <input type="text" class="form-control" id="customer_name" name="customer_name" 
                                           value="{{ $customer ? $customer->full_name : '' }}" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="customer_email" class="form-label">Email Address *</label>
                                    <input type="email" class="form-control" id="customer_email" name="customer_email" 
                                           value="{{ $customer ? $customer->email : '' }}" required>
                                    @if(!Auth::guard('customer')->check())
                                    <small class="text-muted">If you have an account, use your registered email</small>
                                    @endif
                                </div>
                                
                                <div class="mb-3">
                                    <label for="customer_phone" class="form-label">Phone Number *</label>
                                    <input type="tel" class="form-control" id="customer_phone" name="customer_phone" 
                                           value="{{ $customer ? $customer->phone : '' }}" required>
                                </div>
                                
                                <div class="mb-4">
                                    <label for="special_requests" class="form-label">Special Requests</label>
                                    <textarea class="form-control" id="special_requests" name="special_requests" 
                                              rows="3" placeholder="Any special requests or requirements..."></textarea>
                                </div>
                                
                                <!-- Terms & Conditions -->
                                <div class="mb-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="terms" required>
                                        <label class="form-check-label" for="terms">
                                            I agree to the <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">Terms & Conditions</a> *
                                        </label>
                                    </div>
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="checkbox" id="newsletter">
                                        <label class="form-check-label" for="newsletter">
                                            Subscribe to our newsletter for updates and offers
                                        </label>
                                    </div>
                                </div>
                                
                                <!-- Submit Button -->
                                <div class="d-grid">
                                    <button type="submit" id="submit-booking" class="btn btn-success btn-lg" disabled>
                                        <i class="bi bi-check-circle me-2"></i> Confirm Booking
                                    </button>
                                </div>
                                
                                <div class="text-center mt-3">
                                    <small class="text-muted">
                                        <i class="bi bi-lock me-1"></i> Your information is secure
                                    </small>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Terms & Conditions Modal -->
<div class="modal fade" id="termsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Terms & Conditions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <h6>Cancellation Policy</h6>
                <p>You can cancel your booking free of charge up to 48 hours before check-in. Cancellations within 48 hours may incur a one-night charge.</p>
                
                <h6 class="mt-3">Check-in/Check-out</h6>
                <p>Check-in time is 3:00 PM and check-out time is 11:00 AM. Early check-in and late check-out are subject to availability.</p>
                
                <h6 class="mt-3">Payment</h6>
                <p>Payment will be collected at the hotel upon check-in. We accept all major credit cards and cash.</p>
                
                <h6 class="mt-3">Privacy Policy</h6>
                <p>We respect your privacy. Your personal information will only be used for booking purposes and will not be shared with third parties.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal" onclick="document.getElementById('terms').checked = true;">
                    I Agree
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .step {
        position: relative;
        padding: 10px 0;
    }
    
    .step-number {
        width: 40px;
        height: 40px;
        background: #f8f9fa;
        border: 2px solid #dee2e6;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        font-weight: bold;
        color: #6c757d;
    }
    
    .step.active .step-number {
        background: #3498db;
        border-color: #3498db;
        color: white;
    }
    
    .room-option {
        cursor: pointer;
        transition: all 0.3s;
        border: 2px solid transparent;
    }
    
    .room-option:hover {
        border-color: #3498db;
        transform: translateY(-2px);
    }
    
    .room-option.selected {
        border-color: #3498db;
        background-color: rgba(52, 152, 219, 0.05);
    }
    
    #availability-results {
        animation: fadeIn 0.5s;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const roomOptions = document.querySelectorAll('.room-option');
        const checkInDate = document.getElementById('check_in_date');
        const checkOutDate = document.getElementById('check_out_date');
        const checkAvailabilityBtn = document.getElementById('check-availability');
        const availabilityResults = document.getElementById('availability-results');
        const bookingForm = document.getElementById('booking-form');
        const submitBtn = document.getElementById('submit-booking');
        const termsCheckbox = document.getElementById('terms');
        
        let selectedRoomId = '{{ $room ? $room->id : "" }}';
        
        // Room selection
        roomOptions.forEach(option => {
            option.addEventListener('click', function() {
                // Remove selected class from all options
                roomOptions.forEach(opt => opt.classList.remove('selected'));
                
                // Add selected class to clicked option
                this.classList.add('selected');
                
                // Update selected room ID
                selectedRoomId = this.dataset.roomId;
                document.getElementById('room_id').value = selectedRoomId;
                
                // Enable check availability button
                checkAvailabilityBtn.disabled = false;
            });
        });
        
        // Set default room if pre-selected
        if (selectedRoomId) {
            document.querySelector(`.room-option[data-room-id="${selectedRoomId}"]`)?.classList.add('selected');
            document.getElementById('room_id').value = selectedRoomId;
        }
        
        // Date validation
        checkInDate.addEventListener('change', function() {
            const nextDay = new Date(this.value);
            nextDay.setDate(nextDay.getDate() + 1);
            checkOutDate.min = nextDay.toISOString().split('T')[0];
            
            // If current check-out is before new min, update it
            if (new Date(checkOutDate.value) < nextDay) {
                checkOutDate.value = checkOutDate.min;
            }
        });
        
        // Check availability
        checkAvailabilityBtn.addEventListener('click', function() {
            if (!selectedRoomId) {
                alert('Please select a room first.');
                return;
            }
            
            if (!checkInDate.value || !checkOutDate.value) {
                alert('Please select check-in and check-out dates.');
                return;
            }
            
            // Show loading
            this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Checking...';
            this.disabled = true;
            
            // Make API call
            fetch('/bookings/check-availability', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    room_id: selectedRoomId,
                    check_in_date: checkInDate.value,
                    check_out_date: checkOutDate.value
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show availability results
                    document.getElementById('selected-room').textContent = 
                        `${data.data.room.room_number} - ${data.data.room.type}`;
                    document.getElementById('selected-checkin').textContent = 
                        new Date(data.data.check_in_date).toLocaleDateString();
                    document.getElementById('selected-checkout').textContent = 
                        new Date(data.data.check_out_date).toLocaleDateString();
                    document.getElementById('selected-nights').textContent = data.data.nights;
                    document.getElementById('selected-price').textContent = 
                        '$' + parseFloat(data.data.price_per_night).toFixed(2);
                    document.getElementById('selected-total').textContent = data.data.formatted_total;
                    
                    // Set hidden form fields
                    document.getElementById('final_check_in_date').value = data.data.check_in_date;
                    document.getElementById('final_check_out_date').value = data.data.check_out_date;
                    
                    // Show results
                    availabilityResults.classList.remove('d-none');
                    
                    // Enable submit button
                    submitBtn.disabled = false;
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while checking availability. Please try again.');
            })
            .finally(() => {
                // Reset button
                this.innerHTML = '<i class="bi bi-search me-2"></i> Check Availability & Price';
                this.disabled = false;
            });
        });
        
        // Form validation
        bookingForm.addEventListener('submit', function(e) {
            if (!termsCheckbox.checked) {
                e.preventDefault();
                alert('Please agree to the Terms & Conditions.');
                return;
            }
            
            // Validate room and dates are selected
            if (!selectedRoomId || !checkInDate.value || !checkOutDate.value) {
                e.preventDefault();
                alert('Please select a room and dates first.');
                return;
            }
            
            // Show loading on submit
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Processing...';
            submitBtn.disabled = true;
        });
        
        // Enable submit button when terms are checked
        termsCheckbox.addEventListener('change', function() {
            submitBtn.disabled = !this.checked;
        });
    });
</script>
@endsection