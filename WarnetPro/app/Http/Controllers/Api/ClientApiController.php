<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Computer;
use App\Models\Member;
use App\Models\PcCommand;
use App\Models\PlaySession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ClientApiController extends Controller
{
    /**
     * Client heartbeat — called every 5 seconds by each PC client.
     * Updates last_heartbeat, ip_address, mac_address.
     */
    public function heartbeat(Request $request)
    {
        $request->validate([
            'pc_name' => 'required|string',
            'ip_address' => 'nullable|string',
            'mac_address' => 'nullable|string',
        ]);

        $computer = Computer::where('pc_name', $request->pc_name)->first();

        if (!$computer) {
            // Auto-discovery: buat PC dengan status 'unregistered'
            // PC ini akan muncul di Network Scanner operator
            $computer = Computer::create([
                'pc_name'        => $request->pc_name,
                'status'         => 'unregistered',
                'last_heartbeat' => now(),
                'ip_address'     => $request->ip_address,
                'mac_address'    => $request->mac_address,
            ]);

            return response()->json([
                'status'  => 'discovered',
                'message' => 'PC terdeteksi. Menunggu registrasi oleh admin.',
            ]);
        }

        $computer->update([
            'last_heartbeat' => now(),
            'ip_address'     => $request->ip_address,
            'mac_address'    => $request->mac_address,
        ]);

        // Jika PC masih unregistered, beri tahu client
        if ($computer->status === 'unregistered') {
            return response()->json([
                'status'  => 'discovered',
                'message' => 'PC menunggu registrasi oleh admin.',
            ]);
        }

        return response()->json(['status' => 'ok']);
    }

    /**
     * Get the current status of a PC — called every 3 seconds by client.
     * Returns session info, member info, remaining time.
     */
    public function status(string $pcName)
    {
        $computer = Computer::with(['activeSession.member', 'activeBooking'])
            ->where('pc_name', $pcName)
            ->first();

        if (!$computer) {
            return response()->json(['error' => 'PC not found'], 404);
        }

        $data = [
            'pc_name' => $computer->pc_name,
            'status' => $computer->status,
            'session' => null,
        ];

        if ($computer->activeSession) {
            $session = $computer->activeSession;
            $data['session'] = [
                'id' => $session->id,
                'customer_name' => $session->display_name,
                'is_member' => $session->member_id !== null,
                'member_name' => $session->member ? $session->member->name : null,
                'duration_minutes' => $session->duration_minutes,
                'remaining_seconds' => $session->getRealRemainingSeconds(),
                'start_time' => $session->start_time->format('H:i'),
                'end_time' => $session->end_time ? $session->end_time->format('H:i') : null,
            ];
        }

        return response()->json($data);
    }

    /**
     * Member login from client lock screen.
     * Creates a new session using the member's remaining time.
     */
    public function memberLogin(Request $request)
    {
        $request->validate([
            'pc_name' => 'required|string',
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Find the computer
        $computer = Computer::where('pc_name', $request->pc_name)->first();
        if (!$computer) {
            return response()->json(['error' => 'PC tidak ditemukan'], 404);
        }

        // Check PC is available
        if ($computer->status !== 'available') {
            return response()->json(['error' => 'PC sedang tidak tersedia'], 403);
        }

        // Find and verify member
        $member = Member::where('username', $request->username)->first();
        if (!$member || !$member->verifyPassword($request->password)) {
            return response()->json(['error' => 'Username atau password salah'], 401);
        }

        if (!$member->isActive()) {
            return response()->json(['error' => 'Akun member di-suspend'], 403);
        }

        if ($member->remaining_minutes <= 0) {
            return response()->json(['error' => 'Waktu habis. Hubungi operator untuk tambah waktu.'], 403);
        }

        // Check member doesn't already have an active session on another PC
        $existingSession = PlaySession::where('member_id', $member->id)
            ->where('status', 'playing')
            ->first();
        if ($existingSession) {
            return response()->json([
                'error' => 'Member sudah login di ' . $existingSession->computer->pc_name
            ], 403);
        }

        $duration = $member->remaining_minutes;

        // Create play session
        PlaySession::create([
            'computer_id' => $computer->id,
            'member_id' => $member->id,
            'customer_name' => $member->name,
            'start_time' => now(),
            'end_time' => now()->addMinutes($duration),
            'duration_minutes' => $duration,
            'remaining_minutes' => $duration,
            'status' => 'playing',
            'created_by' => 1, // System/auto
        ]);

        // Set member remaining to 0 (time is now "in the session")
        $member->update(['remaining_minutes' => 0]);

        // Update computer status
        $computer->update(['status' => 'used']);

        return response()->json([
            'status' => 'ok',
            'message' => 'Login berhasil! Selamat bermain, ' . $member->name,
            'session' => [
                'member_name' => $member->name,
                'duration_minutes' => $duration,
                'remaining_seconds' => $duration * 60,
            ],
        ]);
    }

    /**
     * Member logout from client — pauses session and saves remaining time.
     */
    public function memberLogout(Request $request)
    {
        $request->validate([
            'pc_name' => 'required|string',
        ]);

        $computer = Computer::where('pc_name', $request->pc_name)->first();
        if (!$computer) {
            return response()->json(['error' => 'PC tidak ditemukan'], 404);
        }

        $session = PlaySession::where('computer_id', $computer->id)
            ->where('status', 'playing')
            ->whereNotNull('member_id')
            ->first();

        if (!$session) {
            return response()->json(['error' => 'Tidak ada sesi member aktif'], 404);
        }

        // Calculate remaining time
        $remainingMinutes = $session->getRealRemainingMinutes();
        $usedMinutes = $session->duration_minutes - $remainingMinutes;

        // Save remaining time back to member
        $member = $session->member;
        $member->update([
            'remaining_minutes' => $remainingMinutes,
        ]);
        $member->recordUsage($usedMinutes);

        // End the session
        $session->update([
            'status' => 'finished',
            'end_time' => now(),
            'remaining_minutes' => 0,
        ]);

        // Set computer back to available
        $computer->update(['status' => 'available']);

        return response()->json([
            'status' => 'ok',
            'message' => 'Logout berhasil. Sisa waktu ' . $remainingMinutes . ' menit disimpan.',
            'remaining_minutes' => $remainingMinutes,
        ]);
    }

    /**
     * Mark PC as offline (called when client shuts down).
     */
    public function offline(Request $request)
    {
        $request->validate([
            'pc_name' => 'required|string',
        ]);

        $computer = Computer::where('pc_name', $request->pc_name)->first();
        if (!$computer) {
            return response()->json(['error' => 'PC not found'], 404);
        }

        // If there was an active member session, save remaining time
        $session = PlaySession::where('computer_id', $computer->id)
            ->where('status', 'playing')
            ->whereNotNull('member_id')
            ->first();

        if ($session) {
            $remainingMinutes = $session->getRealRemainingMinutes();
            $usedMinutes = $session->duration_minutes - $remainingMinutes;

            $session->member->update(['remaining_minutes' => $remainingMinutes]);
            $session->member->recordUsage($usedMinutes);

            $session->update([
                'status' => 'finished',
                'end_time' => now(),
                'remaining_minutes' => 0,
            ]);
        }

        // End any non-member active session too
        $guestSession = PlaySession::where('computer_id', $computer->id)
            ->where('status', 'playing')
            ->whereNull('member_id')
            ->first();

        if ($guestSession) {
            $guestSession->update([
                'status' => 'finished',
                'end_time' => now(),
                'remaining_minutes' => 0,
            ]);
        }

        $computer->update(['status' => 'offline']);

        return response()->json(['status' => 'ok']);
    }

    /**
     * Get pending commands for a PC (shutdown, restart, message).
     */
    public function getCommands(string $pcName)
    {
        $computer = Computer::where('pc_name', $pcName)->first();
        if (!$computer) {
            return response()->json(['error' => 'PC not found'], 404);
        }

        $commands = PcCommand::where('computer_id', $computer->id)
            ->where('status', 'pending')
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($cmd) {
                return [
                    'id' => $cmd->id,
                    'type' => $cmd->command_type,
                    'payload' => $cmd->payload,
                ];
            });

        return response()->json(['commands' => $commands]);
    }

    /**
     * Acknowledge that a command was executed.
     */
    public function acknowledgeCommand(int $id)
    {
        $command = PcCommand::find($id);
        if (!$command) {
            return response()->json(['error' => 'Command not found'], 404);
        }

        $command->acknowledge();

        return response()->json(['status' => 'ok']);
    }

    /**
     * Terima upload screenshot dari client dan simpan ke storage publik.
     * Dipanggil oleh client Python saat menerima command 'screenshot_request'.
     */
    public function uploadScreenshot(Request $request)
    {
        $request->validate([
            'pc_name'    => 'required|string',
            'screenshot' => 'required|image|mimes:jpeg,jpg|max:10240',
        ]);

        $computer = Computer::where('pc_name', $request->pc_name)->first();
        if (!$computer) {
            return response()->json(['error' => 'PC not found'], 404);
        }

        // Ensure screenshots directory exists
        Storage::disk('public')->makeDirectory('screenshots');

        // Simpan dengan nama: {pc_name}_latest.jpg (overwrite setiap kali)
        $filename = $request->pc_name . '_latest.jpg';
        $path = $request->file('screenshot')->storeAs(
            'screenshots', $filename, 'public'
        );

        return response()->json([
            'status'    => 'ok',
            'pc_name'   => $request->pc_name,
            'filename'  => $filename,
            'timestamp' => now()->timestamp,
        ]);
    }

    /**
     * Kembalikan URL screenshot terbaru untuk sebuah PC.
     * Dipanggil oleh dashboard operator via polling AJAX.
     */
    public function getLatestScreenshot(string $pcName)
    {
        $filename = $pcName . '_latest.jpg';
        $path     = 'screenshots/' . $filename;

        if (!Storage::disk('public')->exists($path)) {
            return response()->json(['status' => 'not_found'], 404);
        }

        return response()->json([
            'status'    => 'ok',
            'url'       => asset('storage/' . $path),
            'timestamp' => Storage::disk('public')->lastModified($path),
            'pc_name'   => $pcName,
        ]);
    }
}
