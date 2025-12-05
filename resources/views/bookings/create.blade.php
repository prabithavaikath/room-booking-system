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
                                <input type="hidden" id="calculated_total" name="total_amount">
                                
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
                                
                                <!-- Payment Method Selection -->
                                <div class="mb-4">
                                    <label class="form-label fw-bold">Payment Method</label>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="payment_method" id="pay_at_hotel" value="hotel" checked>
                                        <label class="form-check-label" for="pay_at_hotel">
                                            Pay at Hotel
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_method" id="pay_now" value="stripe">
                                        <label class="form-check-label" for="pay_now">
                                            <i class="bi bi-credit-card me-1"></i> Pay Now with Card
                                        </label>
                                    </div>
                                </div>
                                
                                <!-- Stripe Card Element (hidden by default) -->
                                <div id="stripe-card-element" class="d-none mb-4">
                                    <div class="card border p-3">
                                        <div class="mb-3">
                                            <label for="card-element" class="form-label">Card Details</label>
                                            <div id="card-element" class="form-control p-2" style="height: 45px;"></div>
                                            <div id="card-errors" class="text-danger mt-2 small"></div>
                                        </div>
                                        <div id="card-holder-name" class="mb-3">
                                            <label for="cardholder-name" class="form-label">Cardholder Name</label>
                                            <input type="text" id="cardholder-name" class="form-control" placeholder="Name on card">
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Payment Summary -->
                                <div class="card bg-light mb-4">
                                    <div class="card-body">
                                        <h6 class="fw-bold mb-3">Payment Summary</h6>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Room Price:</span>
                                            <span id="summary-room-price">$0.00</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Tax (10%):</span>
                                            <span id="summary-tax">$0.00</span>
                                        </div>
                                        <hr>
                                        <div class="d-flex justify-content-between fw-bold">
                                            <span>Total Amount:</span>
                                            <span id="summary-total">$0.00</span>
                                        </div>
                                    </div>
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
                                
                                <!-- Submit Buttons -->
                                <div class="d-grid gap-2">
                                    <button type="submit" id="submit-booking" class="btn btn-success btn-lg" disabled>
                                        <i class="bi bi-check-circle me-2"></i> Confirm Booking (Pay at Hotel)
                                    </button>
                                    <button type="button" id="pay-now-btn" class="btn btn-primary btn-lg d-none">
                                        <i class="bi bi-credit-card me-2"></i> Pay Now
                                    </button>
                                </div>
                                
                                <div class="text-center mt-3">
                                    <small class="text-muted">
                                        <i class="bi bi-lock me-1"></i> Secure payment powered by Stripe
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
        transition: all 0.3s ease;
        border: 2px solid transparent;
        border-radius: 8px;
    }
    
    .room-option:hover {
        border-color: #3498db;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    .room-option.selected {
        border: 3px solid #0d6efd !important;
        background-color: #e7f1ff !important;
    }

    .room-option.selected .text-primary,
    .room-option.selected small {
        color: #0d6efd !important;
    }

    .room-option.selected .badge {
        background-color: #0d6efd !important;
        color: #fff !important;
    }

    #availability-results {
        animation: fadeIn 0.5s;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    /* Stripe Element Styles */
    .StripeElement {
        box-sizing: border-box;
        padding: 10px 12px;
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
        background-color: white;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }

    .StripeElement--focus {
        border-color: #86b7fe;
        outline: 0;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }

    .StripeElement--invalid {
        border-color: #dc3545;
    }

    .StripeElement--webkit-autofill {
        background-color: #fefde5 !important;
    }
</style>
@endsection

@section('scripts')
<script src="https://js.stripe.com/v3/"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Stripe with your public key
        const stripe = Stripe('{{ config("services.stripe.key") }}');
        let elements = null;
        let cardElement = null;
        let paymentIntentClientSecret = null;
        
        // Payment method selection
        const paymentMethods = document.querySelectorAll('input[name="payment_method"]');
        const stripeCardElement = document.getElementById('stripe-card-element');
        const submitBtn = document.getElementById('submit-booking');
        const payNowBtn = document.getElementById('pay-now-btn');
        const cardholderNameInput = document.getElementById('cardholder-name');
        const termsCheckbox = document.getElementById('terms');
        
        // Room selection variables
        const roomOptions = document.querySelectorAll('.room-option');
        const checkInDate = document.getElementById('check_in_date');
        const checkOutDate = document.getElementById('check_out_date');
        const checkAvailabilityBtn = document.getElementById('check-availability');
        const availabilityResults = document.getElementById('availability-results');
        
        // Initialize selected room ID from server or from stored selection
        let selectedRoomId = '{{ $room ? $room->id : "" }}';
        let currentTotal = 0;
        
        // Function to select a room
        function selectRoom(roomElement) {
            // Remove selected class from all options
            roomOptions.forEach(opt => {
                opt.classList.remove('selected');
            });
            
            // Add selected class to clicked option
            roomElement.classList.add('selected');
            
            // Update selected room ID
            selectedRoomId = roomElement.dataset.roomId;
            document.getElementById('room_id').value = selectedRoomId;
            
            // Enable check availability button
            checkAvailabilityBtn.disabled = false;
            
            // Clear previous availability results
            availabilityResults.classList.add('d-none');
            submitBtn.disabled = true;
            payNowBtn.disabled = true;
        }
        
        // Room selection click handler
        roomOptions.forEach(option => {
            option.addEventListener('click', function() {
                selectRoom(this);
            });
        });
        
        // Set default room if pre-selected from server
        if (selectedRoomId) {
            const preSelectedRoom = document.querySelector(`.room-option[data-room-id="${selectedRoomId}"]`);
            if (preSelectedRoom) {
                preSelectedRoom.classList.add('selected');
                document.getElementById('room_id').value = selectedRoomId;
                checkAvailabilityBtn.disabled = false;
            }
        }
        
        // Date validation
        checkInDate.addEventListener('change', function() {
            if (this.value) {
                const nextDay = new Date(this.value);
                nextDay.setDate(nextDay.getDate() + 1);
                checkOutDate.min = nextDay.toISOString().split('T')[0];
                
                if (checkOutDate.value && new Date(checkOutDate.value) < nextDay) {
                    checkOutDate.value = checkOutDate.min;
                }
            }
        });
        
        // Update payment summary
 // Update payment summary
function updatePaymentSummary(total) {
    // Ensure total is positive
    const positiveTotal = Math.abs(parseFloat(total) || 0);
    const roomPrice = positiveTotal;
    const tax = positiveTotal * 0.10; // 10% tax
    const totalWithTax = parseFloat((roomPrice + tax).toFixed(2));
    
    document.getElementById('summary-room-price').textContent = roomPrice.toFixed(2);
    document.getElementById('summary-tax').textContent =  tax.toFixed(2);
    document.getElementById('summary-total').textContent =  totalWithTax.toFixed(2);
    document.getElementById('calculated_total').value = totalWithTax.toFixed(2);
    
    // Debug
    console.log('Payment Summary:', {
        roomPrice: roomPrice,
        tax: tax,
        totalWithTax: totalWithTax,
        calculatedTotal: document.getElementById('calculated_total').value
    });
    
    // Update cardholder name if empty
    if (cardholderNameInput && !cardholderNameInput.value) {
        cardholderNameInput.value = document.getElementById('customer_name').value;
    }
}
        // Check availability
        checkAvailabilityBtn.addEventListener('click', async function() {
            if (!selectedRoomId) {
                alert('Please select a room first.');
                return;
            }
            
            if (!checkInDate.value || !checkOutDate.value) {
                alert('Please select check-in and check-out dates.');
                return;
            }
            
            // Show loading
            const originalText = this.innerHTML;
            this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Checking...';
            this.disabled = true;
            
            try {
                const response = await fetch('{{ route("bookings.check-availability") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        room_id: selectedRoomId,
                        check_in_date: checkInDate.value,
                        check_out_date: checkOutDate.value
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Show availability results
                    document.getElementById('selected-room').textContent = 
                        `${data.data.room.room_number} - ${data.data.room.type}`;
                    document.getElementById('selected-checkin').textContent = 
                        new Date(data.data.check_in_date).toLocaleDateString('en-US', {
                            weekday: 'short',
                            year: 'numeric',
                            month: 'short',
                            day: 'numeric'
                        });
                    document.getElementById('selected-checkout').textContent = 
                        new Date(data.data.check_out_date).toLocaleDateString('en-US', {
                            weekday: 'short',
                            year: 'numeric',
                            month: 'short',
                            day: 'numeric'
                        });
                    document.getElementById('selected-nights').textContent = Math.abs(data.data.nights);
                    document.getElementById('selected-price').textContent = 
                        '$' + parseFloat(data.data.price_per_night).toFixed(2);

                        
                   // document.getElementById('selected-total').textContent = Math.abs(parseFloat(data.data.formatted_total).toFixed(2));
                     let totalAmount = 0;
    
    if (data.data.formatted_total) {
        // Remove any non-numeric characters except decimal point
        const totalStr = data.data.formatted_total.toString().replace(/[^\d.-]/g, '');
        totalAmount = Math.abs(parseFloat(totalStr) || 0);
    } else if (data.data.total_amount) {
        totalAmount = Math.abs(parseFloat(data.data.total_amount) || 0);
    }
        document.getElementById('selected-total').textContent =  totalAmount.toFixed(2); 
                    // Set hidden form fields
                    document.getElementById('final_check_in_date').value = data.data.check_in_date;
                    document.getElementById('final_check_out_date').value = data.data.check_out_date;
                    
                    // Update payment summary
                    updatePaymentSummary(data.data.total_amount);
                    
                    // Show results
                    availabilityResults.classList.remove('d-none');
                    
                    // Enable submit buttons based on terms
                    if (termsCheckbox.checked) {
                        submitBtn.disabled = false;
                        payNowBtn.disabled = false;
                    }
                } else {
                    alert(data.message);
                    availabilityResults.classList.add('d-none');
                    submitBtn.disabled = true;
                    payNowBtn.disabled = true;
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while checking availability. Please try again.');
            } finally {
                this.innerHTML = originalText;
                this.disabled = false;
            }
        });
        
        // Payment method toggle
        paymentMethods.forEach(method => {
            method.addEventListener('change', function() {
                if (this.value === 'stripe') {
                    stripeCardElement.classList.remove('d-none');
                    submitBtn.classList.add('d-none');
                    payNowBtn.classList.remove('d-none');
                    
                    // Initialize Stripe Elements if not already done
                    if (!elements) {
                        initializeStripe();
                    }
                    
                    // Enable/disable based on availability and terms
                    if (termsCheckbox.checked && !availabilityResults.classList.contains('d-none')) {
                        payNowBtn.disabled = false;
                    } else {
                        payNowBtn.disabled = true;
                    }
                } else {
                    stripeCardElement.classList.add('d-none');
                    submitBtn.classList.remove('d-none');
                    payNowBtn.classList.add('d-none');
                    
                    if (termsCheckbox.checked && !availabilityResults.classList.contains('d-none')) {
                        submitBtn.disabled = false;
                    }
                }
            });
        });
        
        function initializeStripe() {
            elements = stripe.elements();
            cardElement = elements.create('card', {
                style: {
                    base: {
                        fontSize: '16px',
                        color: '#32325d',
                        '::placeholder': {
                            color: '#aab7c4'
                        }
                    }
                }
            });
            
            cardElement.mount('#card-element');
            
            // Handle real-time validation errors
            cardElement.on('change', function(event) {
                const displayError = document.getElementById('card-errors');
                if (event.error) {
                    displayError.textContent = event.error.message;
                    payNowBtn.disabled = true;
                } else {
                    displayError.textContent = '';
                    if (termsCheckbox.checked && !availabilityResults.classList.contains('d-none')) {
                        payNowBtn.disabled = false;
                    }
                }
            });
        }
        
        // Handle Pay Now button click
 // Handle Pay Now button click
payNowBtn.addEventListener('click', async function(e) {
    e.preventDefault();
    
    // Get and validate amount
    const amountInput = document.getElementById('calculated_total').value;
    const amount = Math.abs(parseFloat(amountInput) || 0);
    
    // Validate amount
    if (isNaN(amount) || amount <= 0.50) { // Minimum $0.50 for Stripe
        alert('Invalid amount. Minimum payment is $0.50. Please check the total amount.');
        return;
    }
    
    // Get and validate nights - extract number from text
    const nightsText = document.getElementById('selected-nights').textContent;
    const nightsMatch = nightsText.match(/\d+/);
    let nights = nightsMatch ? Math.abs(parseInt(nightsMatch[0])) : 1;
    
    if (isNaN(nights) || nights <= 0) {
        nights = 1; // Default to 1 night
    }
    
    // Debug logging
    console.log('Payment Details:', {
        amount: amount,
        nights: nights,
        nightsText: nightsText,
        roomName: document.getElementById('selected-room').textContent
    });
    
    // Validate card details
    const {error: stripeError} = await stripe.createPaymentMethod({
        type: 'card',
        card: cardElement,
        billing_details: {
            name: cardholderNameInput.value || document.getElementById('customer_name').value,
            email: document.getElementById('customer_email').value,
            phone: document.getElementById('customer_phone').value,
        }
    });
    
    if (stripeError) {
        document.getElementById('card-errors').textContent = stripeError.message;
        return;
    }
    
    const originalText = this.innerHTML;
    this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Processing...';
    this.disabled = true;
    
    // Get booking details
    const bookingData = {
        room_id: document.getElementById('room_id').value,
        check_in_date: document.getElementById('final_check_in_date').value,
        check_out_date: document.getElementById('final_check_out_date').value,
        customer_name: document.getElementById('customer_name').value,
        customer_email: document.getElementById('customer_email').value,
        customer_phone: document.getElementById('customer_phone').value,
        special_requests: document.getElementById('special_requests').value,
        total_amount: amount,
        payment_method: 'stripe',
        _token: '{{ csrf_token() }}'
    };
    
    try {
        // First, create the booking
        const bookingResponse = await fetch('{{ route("bookings.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(bookingData)
        });
        
        const bookingResult = await bookingResponse.json();
        
        if (!bookingResult.success) {
            throw new Error(bookingResult.message || 'Booking failed');
        }
        
        // Create Stripe Checkout Session
        const sessionResponse = await fetch('{{ route("payment.session") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                amount: amount,
                booking_id: bookingResult.booking.id,
                customer_email: document.getElementById('customer_email').value,
                room_name: document.getElementById('selected-room').textContent,
                nights: nights
            })
        });
        
        const sessionResult = await sessionResponse.json();
        
        if (sessionResult.error) {
            throw new Error(sessionResult.error);
        }
        
        // Redirect to Stripe Checkout
        window.location.href = sessionResult.url;
        
    } catch (error) {
        console.error('Error:', error);
        alert('Payment failed: ' + error.message);
        this.innerHTML = originalText;
        this.disabled = false;
    }
});
        // Form validation for pay at hotel
        document.getElementById('booking-form').addEventListener('submit', function(e) {
            const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
            
            if (paymentMethod === 'stripe') {
                e.preventDefault();
                alert('Please use the "Pay Now" button for card payments.');
                return;
            }
            
            if (!termsCheckbox.checked) {
                e.preventDefault();
                alert('Please agree to the Terms & Conditions.');
                return;
            }
            
            if (!selectedRoomId || !checkInDate.value || !checkOutDate.value) {
                e.preventDefault();
                alert('Please select a room and dates first.');
                return;
            }
            
            if (availabilityResults.classList.contains('d-none')) {
                e.preventDefault();
                alert('Please check availability before submitting.');
                return;
            }
            
            // Show loading on submit
            const submitText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Processing...';
            submitBtn.disabled = true;
            
            // Re-enable after 5 seconds if still on page (as fallback)
            setTimeout(() => {
                submitBtn.innerHTML = submitText;
                submitBtn.disabled = false;
            }, 5000);
        });
        
        // Enable/disable buttons based on terms checkbox
        termsCheckbox.addEventListener('change', function() {
            if (this.checked && !availabilityResults.classList.contains('d-none')) {
                const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
                if (paymentMethod === 'stripe') {
                    payNowBtn.disabled = false;
                } else {
                    submitBtn.disabled = false;
                }
            } else {
                submitBtn.disabled = true;
                payNowBtn.disabled = true;
            }
        });
        
        // Initialize date constraints
        const today = new Date().toISOString().split('T')[0];
        checkInDate.min = today;
        
        if (checkInDate.value) {
            const nextDay = new Date(checkInDate.value);
            nextDay.setDate(nextDay.getDate() + 1);
            checkOutDate.min = nextDay.toISOString().split('T')[0];
            
            if (!checkOutDate.value) {
                checkOutDate.value = nextDay.toISOString().split('T')[0];
            }
        }
        
        // Also disable check availability button initially if no room selected
        if (!selectedRoomId) {
            checkAvailabilityBtn.disabled = true;
        }
    });
</script>
@endsection