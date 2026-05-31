<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'computer_id',
        'customer_name',
        'booking_start',
        'booking_end',
        'status',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'booking_start' => 'datetime',
            'booking_end' => 'datetime',
        ];
    }

    /**
     * Get the computer for this booking.
     */
    public function computer()
    {
        return $this->belongsTo(Computer::class);
    }

    /**
     * Get the user who created this booking.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the play session linked to this booking.
     */
    public function playSession()
    {
        return $this->hasOne(PlaySession::class);
    }
}