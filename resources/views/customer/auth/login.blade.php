@extends('customer.layouts.app')

@section('title', 'Customer Login')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card customer-card">
            <div class="card-body p-5">
                <div class="text-center mb-4">
                    <h3 class="fw-bold">Customer Login</h3>
                    <p class="text-muted">Sign in to your account</p>
                </div>

                <form method="POST" action="{{ route('customer.login') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                               id="email" name="email" value="{{ old('email') }}" required autofocus>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" 
                               id="password" name="password" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                        <label class="form-check-label" for="remember">Remember me</label>
                    </div>

                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-box-arrow-in-right me-2"></i> Login
                        </button>
                    </div>

                    <div class="text-center">
                        <p class="mb-0">Don't have an account? 
                            <a href="{{ route('customer.register') }}" class="text-decoration-none">Register here</a>
                        </p>
                        <p class="mt-2 mb-0">
                            <a href="/" class="text-decoration-none">
                                <i class="bi bi-arrow-left"></i> Back to Home
                            </a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection