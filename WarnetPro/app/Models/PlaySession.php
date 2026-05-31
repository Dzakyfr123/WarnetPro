<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlaySession extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'computer_id',
        'booking_id',
        'member_id',
        'customer_name',
        'start_time',
        'end_time',
        'duration_minutes',
        'remaining_minutes',
        'status',
        'created_by',
        'started_at',
        'ended_at',
        'last_heartbeat',
        'rate_per_minute',
        'total_cost',
        'client_ip_address',
        'client_mac_address',
        'is_suspicious',
        'activity_log',
    ];

    protected function casts(): array
    {
        return [
            'start_time' => 'datetime',
            'end_time' => 'datetime',
            'started_at' => 'datetime',
            'ended_at' => 'datetime',
            'last_heartbeat' => 'datetime',
            'activity_log' => 'json',
            'is_suspicious' => 'boolean',
        ];
    }

    /**
     * Get the computer for this session.
     */
    public function computer()
    {
        return $this->belongsTo(Computer::class);
    }

    /**
     * Get the booking linked to this session.
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Get the member linked to this session.
     */
    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    /**
     * Get the user who created this session.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get display name (member name, customer name, or "Guest").
     */
    public function getDisplayNameAttribute(): string
    {
        if ($this->member) {
            return $this->member->name;
        }
        return $this->customer_name ?? 'Guest';
    }

    /**
     * Calculate real remaining minutes based on elapsed time.
     */
    public function getRealRemainingMinutes(): int
    {
        if ($this->status === 'finished') {
            return 0;
        }

        $elapsed = now()->diffInSeconds($this->start_time);
        $totalSeconds = $this->duration_minutes * 60;
        $remaining = $totalSeconds - $elapsed;

        return max(0, (int) ceil($remaining / 60));
    }

    /**
     * Calculate real remaining seconds based on elapsed time.
     */
    public function getRealRemainingSeconds(): int
    {
        if ($this->status === 'finished') {
            return 0;
        }

        $elapsed = now()->diffInSeconds($this->start_time);
        $totalSeconds = $this->duration_minutes * 60;
        $remaining = $totalSeconds - $elapsed;
        return max(0, (int) $remaining);
    }

    /**
     * Auto-finish expired sessions.
     */
    public static function autoFinishExpiredSessions()
    {
        $expiredSessions = self::where('status', 'playing')
            ->where('end_time', '<=', now())
            ->get();

        foreach ($expiredSessions as $session) {
            $session->update([
                'status' => 'finished',
                'remaining_minutes' => 0,
            ]);
            $session->computer->update(['status' => 'available']);
        }
    }

    /**
     * Calculate remaining time in minutes (Transparent Session)
     */
    public function getRemainingTime()
    {
        if (!$this->started_at || !$this->duration_minutes) {
            return 0;
        }

        $elapsed = $this->started_at->diffInMinutes(now());
        $remaining = $this->duration_minutes - $elapsed;
        
        return max(0, $remaining);
    }

    /**
     * Check if session is still valid (Transparent Session)
     */
    public function isValid(): bool
    {
        return $this->status === 'playing' && $this->getRemainingTime() > 0;
    }

    /**
     * Add activity log (Transparent Session)
     */
    public function logActivity($action, $data = [])
    {
        $log = $this->activity_log ?? [];
        
        $log[] = [
            'timestamp' => now()->toIso8601String(),
            'action' => $action,
            'data' => $data,
        ];
        
        $this->activity_log = $log;
        $this->save();
    }

    /**
     * End session dengan perhitungan final (Transparent Session)
     */
    public function endSession()
    {
        if ($this->status !== 'playing') {
            return false;
        }

        $this->ended_at = now();
        $this->status = 'ended';

        // Hitung actual duration
        $actualDuration = $this->started_at->diffInMinutes($this->ended_at);
        
        // Hitung cost
        $this->total_cost = $actualDuration * $this->rate_per_minute;

        // Log activity
        $this->logActivity('session_ended', [
            'actual_duration_minutes' => $actualDuration,
            'total_cost' => $this->total_cost,
        ]);

        // Check untuk suspicious activity
        if ($actualDuration > $this->duration_minutes + 5) {
            // Lebih lama dari yang dibayar
            $this->is_suspicious = true;
            $this->logActivity('suspicious_activity', [
                'reason' => 'Duration exceeded',
                'expected' => $this->duration_minutes,
                'actual' => $actualDuration,
            ]);
        }

        $this->save();
        
        return true;
    }

    /**
     * Get session status untuk API (Transparent Session)
     */
    public function getStatusForAPI()
    {
        return [
            'id' => $this->id,
            'member' => $this->member->name ?? 'Unknown',
            'pc' => $this->computer->pc_name,
            'status' => $this->status,
            'started_at' => $this->started_at->toIso8601String(),
            'duration_minutes' => $this->duration_minutes,
            'remaining_minutes' => $this->getRemainingTime(),
            'rate_per_minute' => $this->rate_per_minute,
            'total_cost' => $this->total_cost,
            'is_valid' => $this->isValid(),
            'is_suspicious' => $this->is_suspicious,
        ];
    }
}
