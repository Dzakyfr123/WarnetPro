<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Computer extends Model
{
    protected $fillable = [
        'pc_name',
        'status',
        'last_heartbeat',
        'ip_address',
        'mac_address',
    ];

    protected function casts(): array
    {
        return [
            'last_heartbeat' => 'datetime',
        ];
    }

    /**
     * Get all bookings for this computer.
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Get all play sessions for this computer.
     */
    public function playSessions()
    {
        return $this->hasMany(PlaySession::class);
    }

    /**
     * Get the currently active session for this computer.
     */
    public function activeSession()
    {
        return $this->hasOne(PlaySession::class)->where('status', 'playing');
    }

    /**
     * Get the currently active booking for this computer.
     */
    public function activeBooking()
    {
        return $this->hasOne(Booking::class)->where('status', 'active');
    }

    /**
     * Get all commands for this computer.
     */
    public function commands()
    {
        return $this->hasMany(PcCommand::class);
    }

    /**
     * Get pending commands for this computer.
     */
    public function pendingCommands()
    {
        return $this->hasMany(PcCommand::class)->where('status', 'pending');
    }

    /**
     * Check if this PC is online (heartbeat within last 15 seconds).
     */
    public function isOnline(): bool
    {
        return $this->last_heartbeat && $this->last_heartbeat->diffInSeconds(now()) < 15;
    }

    /**
     * Scope to filter by status.
     */
    public function scopeByStatus($query, $status)
    {
        if ($status && $status !== 'all') {
            return $query->where('status', $status);
        }
        return $query->where('status', '!=', 'unregistered');
    }
}
