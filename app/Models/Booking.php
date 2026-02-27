<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Booking extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
  protected $fillable = [
        'room_id',
        'check_in_date',
        'check_out_date',
        'customer_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'special_requests',
        'total_amount',
        'status',
        'payment_status',
        'payment_method',
        'reference_number',
        'stripe_session_id',
        'stripe_payment_intent'
    ];
    

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'check_in_date' => 'datetime',
        'check_out_date' => 'datetime',
        'total_amount' => 'decimal:2',
    ];

       // Define status values
    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_CHECKED_IN = 'checked_in';
    const STATUS_CHECKED_OUT = 'checked_out';
    const STATUS_CANCELLED = 'cancelled';
    
    const PAYMENT_PENDING = 'pending';
    const PAYMENT_PAID = 'paid';
    const PAYMENT_FAILED = 'failed';
    const PAYMENT_REFUNDED = 'refunded';

    /**
     * Get the room that owns the booking.
     */
    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Get the customer that owns the booking.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Calculate total nights
     */
    public static function calculateNights($checkIn, $checkOut)
    {
        $checkIn = Carbon::parse($checkIn);
        $checkOut = Carbon::parse($checkOut);
        return $checkOut->diffInDays($checkIn);
    }

    /**
     * Calculate total amount
     */
    public static function calculateTotalAmount($roomPrice, $nights)
    {
        return $roomPrice * $nights;
    }

    /**
     * Get formatted total amount
     */
    public function getFormattedTotalAttribute()
    {
        return '$' . number_format($this->total_amount, 2);
    }

    /**
     * Get booking status badge color
     */
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'confirmed' => 'success',
            'pending' => 'warning',
            'cancelled' => 'danger',
            'checked_in' => 'info',
            'checked_out' => 'secondary',
        ];

        return $badges[$this->status] ?? 'secondary';
    }

    /**
     * Get formatted booking reference
     */
    public function getBookingReferenceAttribute()
    {
        return 'BOOK-' . str_pad($this->id, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Scope for active bookings
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['confirmed', 'checked_in']);
    }

    /**
     * Scope for upcoming bookings
     */
    public function scopeUpcoming($query)
    {
        return $query->where('check_in_date', '>', now());
    }

    /**
     * Scope for current bookings
     */
    public function scopeCurrent($query)
    {
        return $query->where('check_in_date', '<=', now())
                    ->where('check_out_date', '>=', now());
    }

    /**
     * Check if booking can be cancelled
     */
    public function canBeCancelled()
    {
        // Can cancel if check-in is more than 24 hours away
        return Carbon::parse($this->check_in_date)->diffInHours(now()) > 24;
    }
}