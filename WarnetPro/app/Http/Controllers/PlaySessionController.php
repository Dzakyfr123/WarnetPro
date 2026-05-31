<?php

namespace App\Http\Controllers;

use App\Models\PlaySession;
use App\Models\Computer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PlaySessionController extends Controller
{
    /**
     * Display history of play sessions.
     */
    public function index(Request $request)
    {
        PlaySession::autoFinishExpiredSessions();
        $status = $request->get('status', 'all');

        $query = PlaySession::with(['computer', 'creator', 'member'])
            ->orderBy('created_at', 'desc');

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        $sessions = $query->paginate(15);

        return view('sessions.index', compact('sessions', 'status'));
    }

    /**
     * Show form to start a new session directly (without booking).
     * For guests: operator picks PC + duration only. No name needed.
     */
    public function create()
    {
        $availableComputers = Computer::where('status', 'available')
            ->orderBy('pc_name')
            ->get();

        return view('sessions.create', compact('availableComputers'));
    }

    /**
     * Store a new play session directly.
     * Guest sessions: just PC + duration. No customer name needed.
     */
    public function store(Request $request)
    {
        $request->validate([
            'computer_id' => 'required|exists:computers,id',
            'duration_minutes' => 'required|integer|min:1|max:720',
        ]);

        $computer = Computer::findOrFail($request->computer_id);
        if ($computer->status !== 'available') {
            return redirect()->back()
                ->withInput()
                ->with('error', 'PC tidak tersedia!');
        }

        $duration = (int) $request->duration_minutes;

        PlaySession::create([
            'computer_id' => $request->computer_id,
            'customer_name' => null,
            'start_time' => now(),
            'end_time' => now()->addMinutes($duration),
            'duration_minutes' => $duration,
            'remaining_minutes' => $duration,
            'status' => 'playing',
            'created_by' => Auth::id(),
        ]);

        // Update computer status
        $computer->update(['status' => 'used']);

        return redirect()->route('dashboard')
            ->with('success', 'Sesi bermain dimulai untuk ' . $computer->pc_name . '!');
    }

    /**
     * Add extra time to an active session.
     */
    public function addTime(Request $request, PlaySession $session)
    {
        $request->validate([
            'extra_minutes' => 'required|integer|min:1|max:720',
        ]);

        if ($session->status !== 'playing') {
            return redirect()->back()
                ->with('error', 'Sesi sudah selesai!');
        }

        $extra = (int) $request->extra_minutes;

        $session->update([
            'duration_minutes' => $session->duration_minutes + $extra,
            'remaining_minutes' => $session->remaining_minutes + $extra,
            'end_time' => $session->end_time->addMinutes($extra),
        ]);

        return redirect()->back()
            ->with('success', 'Waktu berhasil ditambahkan!');
    }

    /**
     * End a session manually.
     */
    public function endSession(PlaySession $session)
    {
        if ($session->status !== 'playing') {
            return redirect()->back()
                ->with('error', 'Sesi sudah selesai!');
        }

        // If member session, save remaining time back to member
        if ($session->member_id) {
            $remainingMinutes = $session->getRealRemainingMinutes();
            $usedMinutes = $session->duration_minutes - $remainingMinutes;

            $session->member->update(['remaining_minutes' => $remainingMinutes]);
            $session->member->recordUsage($usedMinutes);
        }

        $session->update([
            'status' => 'finished',
            'end_time' => now(),
            'remaining_minutes' => 0,
        ]);

        // Set computer back to available
        $session->computer->update(['status' => 'available']);

        return redirect()->back()
            ->with('success', 'Sesi berhasil diakhiri!');
    }

    /**
     * Get active sessions as JSON for real-time updates.
     */
    public function getActiveSessionsJson()
    {
        // Auto-finish expired sessions first
        PlaySession::autoFinishExpiredSessions();

        // Get active sessions
        $activeSessions = PlaySession::with(['computer', 'member'])
            ->where('status', 'playing')
            ->orderBy('start_time', 'asc')
            ->get()
            ->map(function ($session) {
                return [
                    'id' => $session->id,
                    'computer_name' => $session->computer->pc_name,
                    'computer_id' => $session->computer_id,
                    'customer_name' => $session->display_name,
                    'is_member' => $session->member_id !== null,
                    'duration_minutes' => $session->duration_minutes,
                    'remaining_seconds' => $session->getRealRemainingSeconds(),
                    'start_time' => $session->start_time->format('H:i'),
                    'end_time' => $session->end_time ? $session->end_time->format('H:i') : null,
                ];
            });

        return response()->json([
            'sessions' => $activeSessions,
        ]);
    }
}
