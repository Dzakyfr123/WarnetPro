<?php

namespace App\Http\Controllers;

use App\Models\Computer;
use App\Models\Member;
use App\Models\PlaySession;
use Illuminate\Http\Request;

class ClientSessionController extends Controller
{
    /**
     * Member login dari client
     * POST /api/client/session/login
     */
    public function login(Request $request)
    {
        $request->validate([
            'pc_name' => 'required|string',
            'member_id' => 'required|integer',
            'duration_minutes' => 'required|integer|min:5',
            'client_ip' => 'nullable|ip',
            'client_mac' => 'nullable|string',
        ]);

        try {
            // Find computer
            $computer = Computer::where('pc_name', $request->pc_name)->firstOrFail();

            // Find member
            $member = Member::findOrFail($request->member_id);

            // Check if member sudah ada session aktif
            $activeSession = PlaySession::where('member_id', $member->id)
                ->where('status', 'playing')
                ->first();

            if ($activeSession) {
                return response()->json([
                    'success' => false,
                    'error' => 'Member sudah memiliki session aktif di PC lain',
                ], 400);
            }

            // Check if PC already in use
            $pcSession = PlaySession::where('computer_id', $computer->id)
                ->where('status', 'playing')
                ->first();

            if ($pcSession) {
                return response()->json([
                    'success' => false,
                    'error' => 'PC sedang digunakan',
                ], 400);
            }

            // Create new session
            $session = PlaySession::create([
                'computer_id' => $computer->id,
                'member_id' => $member->id,
                'status' => 'playing',
                'duration_minutes' => $request->duration_minutes,
                'started_at' => now(),
                'rate_per_minute' => $member->rate_per_minute ?? 500,
                'client_ip_address' => $request->client_ip ?? $request->ip(),
                'client_mac_address' => $request->client_mac,
            ]);

            // Log activity
            $session->logActivity('login', [
                'member' => $member->name,
                'duration' => $request->duration_minutes,
                'client_ip' => $request->client_ip,
            ]);

            // Update computer status
            $computer->update(['status' => 'in_use']);

            return response()->json([
                'success' => true,
                'session' => $session->getStatusForAPI(),
                'message' => 'Login successful',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Member logout dari client
     * POST /api/client/session/logout
     */
    public function logout(Request $request)
    {
        $request->validate([
            'session_id' => 'required|integer',
            'pc_name' => 'required|string',
        ]);

        try {
            $session = PlaySession::findOrFail($request->session_id);
            $computer = Computer::where('pc_name', $request->pc_name)->firstOrFail();

            // Verify session belongs to this PC
            if ($session->computer_id !== $computer->id) {
                return response()->json([
                    'success' => false,
                    'error' => 'Session PC mismatch',
                ], 400);
            }

            // End session
            $session->endSession();

            // Update computer status
            $computer->update(['status' => 'available']);

            return response()->json([
                'success' => true,
                'message' => 'Logout successful',
                'total_cost' => $session->total_cost,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get session status
     * GET /api/client/session/{session_id}
     */
    public function getStatus($sessionId)
    {
        try {
            $session = PlaySession::findOrFail($sessionId);

            if (!$session->isValid()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Session tidak valid atau sudah expired',
                ], 400);
            }

            return response()->json([
                'success' => true,
                'session' => $session->getStatusForAPI(),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Heartbeat dari client (untuk monitor session)
     * POST /api/client/session/heartbeat
     */
    public function heartbeat(Request $request)
    {
        $request->validate([
            'session_id' => 'required|integer',
        ]);

        try {
            $session = PlaySession::findOrFail($request->session_id);

            // Update last heartbeat
            $session->update(['last_heartbeat' => now()]);

            // Log heartbeat
            $session->logActivity('heartbeat', [
                'timestamp' => now()->toIso8601String(),
            ]);

            return response()->json([
                'success' => true,
                'remaining_minutes' => $session->getRemainingTime(),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
