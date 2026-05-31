<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PcCommand extends Model
{
    protected $fillable = [
        'computer_id',
        'command_type',
        'payload',
        'status',
        'created_by',
    ];

    /**
     * Get the computer this command is for.
     */
    public function computer()
    {
        return $this->belongsTo(Computer::class);
    }

    /**
     * Get the user who created this command.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope to get pending commands.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Mark command as acknowledged.
     */
    public function acknowledge(): void
    {
        $this->update(['status' => 'acknowledged']);
    }
}
