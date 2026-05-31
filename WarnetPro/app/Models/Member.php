<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class Member extends Model
{
    protected $fillable = [
        'username',
        'name',
        'password',
        'remaining_minutes',
        'total_usage_minutes',
        'status',
    ];

    protected $hidden = [
        'password',
    ];

    /**
     * Set password as hashed.
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    /**
     * Verify password.
     */
    public function verifyPassword(string $password): bool
    {
        return Hash::check($password, $this->password);
    }

    /**
     * Add time to this member's account.
     */
    public function addTime(int $minutes): void
    {
        $this->increment('remaining_minutes', $minutes);
    }

    /**
     * Deduct time from this member's account.
     */
    public function deductTime(int $minutes): void
    {
        $this->decrement('remaining_minutes', min($minutes, $this->remaining_minutes));
    }

    /**
     * Save remaining session time back to member account.
     */
    public function saveRemainingTime(int $minutes): void
    {
        $this->remaining_minutes = $minutes;
        $this->save();
    }

    /**
     * Record usage time.
     */
    public function recordUsage(int $minutes): void
    {
        $this->increment('total_usage_minutes', $minutes);
    }

    /**
     * Get play sessions for this member.
     */
    public function playSessions()
    {
        return $this->hasMany(PlaySession::class);
    }

    /**
     * Check if member is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Format remaining time as human readable.
     */
    public function getFormattedRemainingTimeAttribute(): string
    {
        $hours = intdiv($this->remaining_minutes, 60);
        $minutes = $this->remaining_minutes % 60;

        if ($hours > 0) {
            return "{$hours} jam {$minutes} menit";
        }
        return "{$minutes} menit";
    }
}