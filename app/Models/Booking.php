<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'customer_name',
        'customer_email',
        'customer_phone',
        'check_in_date',
        'check_out_date',
        'total_nights',
        'total_amount',
        'status',
        'special_requests',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'check_in_date' => 'date',
        'check_out_date' => 'date',
        'total_amount' => 'decimal:2',
    ];

    /**
     * Get the room that owns the booking.
     */
    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Calculate total nights
     */
    public static function calculateNights($checkIn, $checkOut)
    {
        $checkIn = \Carbon\Carbon::parse($checkIn);
        $checkOut = \Carbon\Carbon::parse($checkOut);
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
}