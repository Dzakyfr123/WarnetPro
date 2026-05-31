<?php

namespace App\Http\Controllers;

use App\Models\Computer;
use App\Models\Booking;
use App\Models\Member;
use App\Models\PlaySession;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the dashboard with statistics.
     */
    public function index()
    {
        PlaySession::autoFinishExpiredSessions();
        $stats = [
            'available' => Computer::where('status', 'available')->count(),
            'used' => Computer::where('status', 'used')->count(),
            'booking' => Computer::where('status', 'booking')->count(),
            'offline' => Computer::where('status', 'offline')->count(),
            'total' => Computer::where('status', '!=', 'unregistered')->count(),
        ];


        $activeSessions = PlaySession::with('computer')
            ->where('status', 'playing')
            ->orderBy('start_time', 'asc')
            ->get()
            ->map(function ($session) {
                $session->real_remaining_seconds = $session->getRealRemainingSeconds();
                return $session;
            });

        $recentBookings = Booking::with(['computer', 'creator'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $computers = Computer::with(['activeSession', 'activeBooking'])
            ->where('status', '!=', 'unregistered')
            ->orderBy('pc_name')
            ->get();

        return view('dashboard', compact('stats', 'activeSessions', 'recentBookings', 'computers'));
    }

    /**
     * Get stats as JSON for auto-refresh.
     */
    public function getStatsJson()
    {
        PlaySession::autoFinishExpiredSessions();
        $stats = [
            'available' => Computer::where('status', 'available')->count(),
            'used' => Computer::where('status', 'used')->count(),
            'booking' => Computer::where('status', 'booking')->count(),
            'offline' => Computer::where('status', 'offline')->count(),
            'total' => Computer::where('status', '!=', 'unregistered')->count(),
        ];


        $computers = Computer::with(['activeSession', 'activeBooking'])
            ->where('status', '!=', 'unregistered')
            ->orderBy('pc_name')
            ->get()
            ->map(function ($computer) {
                $data = [
                    'id' => $computer->id,
                    'pc_name' => $computer->pc_name,
                    'status' => $computer->status,
                    'customer_name' => null,
                    'remaining_seconds' => null,
                ];

                if ($computer->activeSession) {
                    $data['customer_name'] = $computer->activeSession->customer_name;
                    $data['remaining_seconds'] = $computer->activeSession->getRealRemainingSeconds();
                } elseif ($computer->activeBooking) {
                    $data['customer_name'] = $computer->activeBooking->customer_name;
                }

                return $data;
            });

        return response()->json([
            'stats' => $stats,
            'computers' => $computers,
        ]);
    }
}
