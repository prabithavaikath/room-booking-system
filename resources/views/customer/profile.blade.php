@extends('customer.layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="welcome-banner">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h2 class="mb-2">My Profile</h2>
            <p class="mb-0">Manage your personal information and preferences</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('customer.dashboard') }}" class="btn btn-light">
                <i class="bi bi-arrow-left me-2"></i> Back to Dashboard
            </a>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mt-4" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row mt-4">
    <!-- Profile Information -->
    <div class="col-md-4 mb-4">
        <div class="card customer-card">
            <div class="card-body text-center">
                <div class="mb-4">
                    <div class="mx-auto rounded-circle bg-primary d-flex align-items-center justify-content-center" 
                         style="width: 120px; height: 120px;">
                        <span class="display-4 text-white">{{ substr($customer->first_name, 0, 1) }}{{ substr($customer->last_name, 0, 1) }}</span>
                    </div>
                </div>
                
                <h4 class="fw-bold mb-2">{{ $customer->full_name }}</h4>
                <p class="text-muted mb-3">{{ $customer->email }}</p>
                
                <div class="d-grid gap-2">
                    <span class="badge bg-success p-2">Active Member</span>
                    <small class="text-muted">Member since {{ $customer->created_at->format('F Y') }}</small>
                </div>
                
                <hr class="my-4">
                
                <div class="text-start">
                    <h6 class="fw-bold mb-3">Account Statistics</h6>
                    <div class="row">
                        <div class="col-6">
                            <div class="text-center p-2 border rounded">
                                <h5 class="mb-1 text-primary">{{ $customer->bookings()->count() }}</h5>
                                <small class="text-muted">Bookings</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-2 border rounded">
                                <h5 class="mb-1 text-success">{{ $customer->bookings()->where('status', 'confirmed')->count() }}</h5>
                                <small class="text-muted">Active</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Quick Links -->
        <div class="card customer-card mt-4">
            <div class="card-body">
                <h6 class="fw-bold mb-3">Quick Links</h6>
                <div class="list-group list-group-flush">
                    <a href="{{ route('customer.bookings') }}" class="list-group-item list-group-item-action">
                        <i class="bi bi-calendar-check me-2"></i> My Bookings
                    </a>
                    <a href="{{ route('bookings.create') }}" class="list-group-item list-group-item-action">
                        <i class="bi bi-plus-circle me-2"></i> New Booking
                    </a>
                    <a href="{{ route('customer.dashboard') }}" class="list-group-item list-group-item-action">
                        <i class="bi bi-speedometer2 me-2"></i> Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Edit Profile Form -->
    <div class="col-md-8">
        <div class="card customer-card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-pencil me-2"></i> Edit Profile Information</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('customer.profile.update') }}">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="first_name" class="form-label">First Name *</label>
                            <input type="text" class="form-control @error('first_name') is-invalid @enderror" 
                                   id="first_name" name="first_name" value="{{ old('first_name', $customer->first_name) }}" required>
                            @error('first_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="last_name" class="form-label">Last Name *</label>
                            <input type="text" class="form-control @error('last_name') is-invalid @enderror" 
                                   id="last_name" name="last_name" value="{{ old('last_name', $customer->last_name) }}" required>
                            @error('last_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address *</label>
                        <input type="email" class="form-control bg-light" id="email" value="{{ $customer->email }}" disabled>
                        <small class="text-muted">Email cannot be changed. Contact support if needed.</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone Number *</label>
                        <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                               id="phone" name="phone" value="{{ old('phone', $customer->phone) }}" required>
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <input type="text" class="form-control @error('address') is-invalid @enderror" 
                               id="address" name="address" value="{{ old('address', $customer->address) }}">
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="city" class="form-label">City</label>
                            <input type="text" class="form-control @error('city') is-invalid @enderror" 
                                   id="city" name="city" value="{{ old('city', $customer->city) }}">
                            @error('city')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="country" class="form-label">Country</label>
                            <input type="text" class="form-control @error('country') is-invalid @enderror" 
                                   id="country" name="country" value="{{ old('country', $customer->country) }}">
                            @error('country')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="state" class="form-label">State/Province</label>
                            <input type="text" class="form-control @error('state') is-invalid @enderror" 
                                   id="state" name="state" value="{{ old('state', $customer->state) }}">
                            @error('state')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="postal_code" class="form-label">Postal Code</label>
                            <input type="text" class="form-control @error('postal_code') is-invalid @enderror" 
                                   id="postal_code" name="postal_code" value="{{ old('postal_code', $customer->postal_code) }}">
                            @error('postal_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="date_of_birth" class="form-label">Date of Birth</label>
                        <input type="date" class="form-control @error('date_of_birth') is-invalid @enderror" 
                               id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', $customer->date_of_birth ? $customer->date_of_birth->format('Y-m-d') : '') }}">
                        @error('date_of_birth')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="reset" class="btn btn-outline-secondary me-md-2">
                            <i class="bi bi-arrow-clockwise me-2"></i> Reset
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i> Update Profile
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Change Password Card -->
        <!-- <div class="card customer-card mt-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-shield-lock me-2"></i> Change Password</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="#" id="change-password-form">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Current Password</label>
                        <input type="password" class="form-control" id="current_password" name="current_password">
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="new_password" class="form-label">New Password</label>
                            <input type="password" class="form-control" id="new_password" name="new_password">
                            <small class="text-muted">Minimum 8 characters</small>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="new_password_confirmation" class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation">
                        </div>
                    </div>
                    
                    <div class="d-grid">
                        <button type="button" class="btn btn-outline-primary" onclick="alert('Password change functionality will be implemented soon.')">
                            <i class="bi bi-key me-2"></i> Change Password
                        </button>
                    </div>
                </form>
            </div>
        </div> -->
    </div>
</div>

<!-- Contact Information Card -->
<div class="card customer-card mt-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-telephone me-2"></i> Contact Information</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h6 class="fw-bold mb-3">Hotel Contact</h6>
                <p class="mb-2"><i class="bi bi-telephone me-2"></i> <strong>Reservations:</strong> +1 (555) 123-4567</p>
                <p class="mb-2"><i class="bi bi-envelope me-2"></i> <strong>Email:</strong> support@royalsuites.com</p>
                <p class="mb-0"><i class="bi bi-clock me-2"></i> <strong>Support Hours:</strong> 24/7</p>
            </div>
            <div class="col-md-6">
                <h6 class="fw-bold mb-3">Need Help?</h6>
                <p class="text-muted">If you need to update your email address or have any other account-related issues, please contact our support team.</p>
                <a href="mailto:support@royalsuites.com" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-envelope me-1"></i> Contact Support
                </a>
            </div>
        </div>
    </div>
</div>
@endsection