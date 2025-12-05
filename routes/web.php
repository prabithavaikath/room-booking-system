<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\Customer\AuthController as CustomerAuthController;
use App\Http\Controllers\Customer\DashboardController as CustomerDashboardController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\CustomerController as AdminCustomerController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\PaymentController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Home and Public Pages
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::get('/rooms', [HomeController::class, 'rooms'])->name('rooms.public');

// Room Public Routes
Route::prefix('rooms')->controller(RoomController::class)->group(function () {
    Route::get('/', 'index')->name('rooms.index');
    Route::get('/{room}', 'show')->name('rooms.show');
});

// Customer Authentication Routes
Route::prefix('customer')->group(function () {
    Route::get('/login', [CustomerAuthController::class, 'showLoginForm'])->name('customer.login');
    Route::post('/login', [CustomerAuthController::class, 'login'])->name('customer.login.submit');
    Route::get('/register', [CustomerAuthController::class, 'showRegisterForm'])->name('customer.register');
    Route::post('/register', [CustomerAuthController::class, 'register'])->name('customer.register.submit');
    Route::post('/logout', [CustomerAuthController::class, 'logout'])->name('customer.logout');
    
    // Protected Customer Routes
    Route::middleware('auth:customer')->group(function () {
        Route::get('/dashboard', [CustomerDashboardController::class, 'index'])->name('customer.dashboard');
        Route::get('/profile', [CustomerDashboardController::class, 'profile'])->name('customer.profile');
        Route::post('/profile', [CustomerDashboardController::class, 'updateProfile'])->name('customer.profile.update');
        Route::get('/bookings', [CustomerDashboardController::class, 'bookings'])->name('customer.bookings');
    });
});

// Admin Authentication Routes
Route::prefix('admin')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
    
    // Protected Admin Routes
    Route::middleware('auth:admin')->group(function () {
        // Dashboard
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
        Route::get('/profile', [AdminDashboardController::class, 'profile'])->name('admin.profile');
        Route::post('/profile', [AdminDashboardController::class, 'updateProfile'])->name('admin.profile.update');
        
        // Room Management (Admin only)
        Route::prefix('rooms')->controller(RoomController::class)->group(function () {
            Route::get('/create', 'create')->name('rooms.create');
            Route::post('/', 'store')->name('rooms.store');
            Route::get('/{room}/edit', 'edit')->name('rooms.edit');
            Route::put('/{room}', 'update')->name('rooms.update');
            Route::delete('/{room}', 'destroy')->name('rooms.destroy');
            Route::post('/{room}/toggle-availability', 'toggleAvailability')->name('rooms.toggle-availability');
        });
        
        // Booking Management (Admin only)
        Route::prefix('bookings')->controller(AdminBookingController::class)->group(function () {
            Route::get('/', 'index')->name('admin.bookings.index');
            Route::get('/create', 'create')->name('admin.bookings.create');
            Route::post('/', 'store')->name('admin.bookings.store');
            Route::get('/calendar', 'calendar')->name('admin.bookings.calendar');
            Route::get('/export', 'export')->name('admin.bookings.export');
            Route::get('/{booking}', 'show')->name('admin.bookings.show');
            Route::get('/{booking}/edit', 'edit')->name('admin.bookings.edit');
            Route::put('/{booking}', 'update')->name('admin.bookings.update');
            Route::delete('/{booking}', 'destroy')->name('admin.bookings.destroy');
            Route::post('/{booking}/status', 'updateStatus')->name('admin.bookings.update-status');
            Route::post('/{booking}/check-in', 'checkIn')->name('admin.bookings.check-in');
            Route::post('/{booking}/check-out', 'checkOut')->name('admin.bookings.check-out');
            Route::post('/{booking}/cancel', 'cancel')->name('admin.bookings.cancel');
        });
        
        // Customer Management (Admin only)
        Route::prefix('customers')->controller(AdminCustomerController::class)->group(function () {
            Route::get('/', 'index')->name('admin.customers.index');
            Route::get('/create', 'create')->name('admin.customers.create');
            Route::post('/', 'store')->name('admin.customers.store');
            Route::get('/{customer}', 'show')->name('admin.customers.show');
            Route::get('/{customer}/edit', 'edit')->name('admin.customers.edit');
            Route::put('/{customer}', 'update')->name('admin.customers.update');
            Route::delete('/{customer}', 'destroy')->name('admin.customers.destroy');
            Route::post('/{customer}/status', 'updateStatus')->name('admin.customers.update-status');
            Route::post('/{customer}/reset-password', 'resetPassword')->name('admin.customers.reset-password');
            Route::get('/export', 'export')->name('admin.customers.export');
        });
    });

    Route::prefix('reports')->controller(ReportController::class)->group(function () {
        Route::get('/', 'index')->name('admin.reports.index');
        Route::get('/bookings', 'bookings')->name('admin.reports.bookings');
        Route::get('/revenue', 'revenue')->name('admin.reports.revenue');
        Route::get('/customers', 'customers')->name('admin.reports.customers');
        Route::get('/export-bookings', 'exportBookings')->name('admin.reports.export-bookings');
        Route::get('/export-revenue', 'exportRevenue')->name('admin.reports.export-revenue');
    });
});

//Payment Routes
Route::post('/create-payment-intent', [PaymentController::class, 'createPaymentIntent'])->name('payment.intent');
Route::post('/create-checkout-session', [PaymentController::class, 'createCheckoutSession'])->name('payment.session');
Route::get('/payment/success', [PaymentController::class, 'success'])->name('payment.success');
Route::get('/payment/cancel', [PaymentController::class, 'cancel'])->name('payment.cancel');
// routes/web.php
Route::get('/payment/cancel', [PaymentController::class, 'cancel'])->name('payment.cancel');
// Webhook route (POST only)
Route::post('/stripe/webhook', [PaymentController::class, 'handleWebhook'])->name('stripe.webhook');
// Booking Routes (accessible to both public and authenticated)
Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');

Route::prefix('bookings')->controller(BookingController::class)->group(function () {
    Route::get('/create', 'create')->name('bookings.create');
    Route::post('/check-availability', 'checkAvailability')->name('bookings.check-availability');
    Route::post('/', 'store')->name('bookings.store');
    Route::get('/{booking}/confirmation', 'confirmation')->name('bookings.confirmation');
    Route::get('/{booking}', 'show')->name('bookings.show');
    Route::delete('/{booking}/cancel', 'cancel')->name('bookings.cancel');
    Route::get('/{booking}/invoice', 'invoice')->name('bookings.invoice');
    
    // Protected routes for authenticated customers
    Route::middleware('auth:customer')->group(function () {
        Route::get('/my-bookings', 'myBookings')->name('bookings.my-bookings');
    });
});