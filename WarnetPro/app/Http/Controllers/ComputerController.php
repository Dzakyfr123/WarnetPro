<?php

namespace App\Http\Controllers;

use App\Models\Computer;
use App\Models\PcCommand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ComputerController extends Controller
{
    /**
     * Display a listing of computers.
     */
    public function index(Request $request)
    {
        \App\Models\PlaySession::autoFinishExpiredSessions();
        $status = $request->get('status', 'all');

        $computers = Computer::with(['activeSession', 'activeBooking'])
            ->byStatus($status)
            ->orderBy('pc_name')
            ->get();

        return view('computers.index', compact('computers', 'status'));
    }

    /**
     * Show the form for creating a new computer.
     */
    public function create()
    {
        return view('computers.create');
    }

    /**
     * Store a newly created computer.
     */
    public function store(Request $request)
    {
        $request->validate([
            'pc_name' => 'required|string|max:255|unique:computers,pc_name',
        ]);

        Computer::create([
            'pc_name' => $request->pc_name,
            'status' => 'available',
        ]);

        return redirect()->route('computers.index')
            ->with('success', 'PC berhasil ditambahkan!');
    }

    /**
     * Show the form for editing a computer.
     */
    public function edit(Computer $computer)
    {
        return view('computers.edit', compact('computer'));
    }

    /**
     * Update the specified computer.
     */
    public function update(Request $request, Computer $computer)
    {
        $request->validate([
            'pc_name' => 'required|string|max:255|unique:computers,pc_name,' . $computer->id,
            'status' => 'required|in:available,booking,used,offline',
        ]);

        $computer->update([
            'pc_name' => $request->pc_name,
            'status' => $request->status,
        ]);

        return redirect()->route('computers.index')
            ->with('success', 'PC berhasil diupdate!');
    }

    /**
     * Remove the specified computer.
     */
    public function destroy(Computer $computer)
    {
        $computer->delete();

        return redirect()->route('computers.index')
            ->with('success', 'PC berhasil dihapus!');
    }

    /**
     * Toggle computer online/offline status.
     */
    public function toggleStatus(Computer $computer)
    {
        if ($computer->status === 'offline') {
            $computer->update(['status' => 'available']);
        } elseif ($computer->status === 'available') {
            $computer->update(['status' => 'offline']);
        }

        return redirect()->back()
            ->with('success', 'Status PC berhasil diubah!');
    }

    /**
     * Send shutdown command to a PC.
     */
    public function shutdown(Computer $computer)
    {
        PcCommand::create([
            'computer_id' => $computer->id,
            'command_type' => 'shutdown',
            'status' => 'pending',
            'created_by' => Auth::id(),
        ]);

        return redirect()->back()
            ->with('success', 'Perintah shutdown dikirim ke ' . $computer->pc_name);
    }

    /**
     * Send restart command to a PC.
     */
    public function restart(Computer $computer)
    {
        PcCommand::create([
            'computer_id' => $computer->id,
            'command_type' => 'restart',
            'status' => 'pending',
            'created_by' => Auth::id(),
        ]);

        return redirect()->back()
            ->with('success', 'Perintah restart dikirim ke ' . $computer->pc_name);
    }

    /**
     * Send lock command to a PC — menampilkan lock screen di client.
     */
    public function lock(Computer $computer)
    {
        PcCommand::create([
            'computer_id' => $computer->id,
            'command_type' => 'lock',
            'status' => 'pending',
            'created_by' => Auth::id(),
        ]);

        return redirect()->back()
            ->with('success', 'PC ' . $computer->pc_name . ' berhasil dikunci!');
    }

    /**
     * Send unlock command to a PC — membuka lock screen di client.
     */
    public function unlock(Computer $computer)
    {
        PcCommand::create([
            'computer_id' => $computer->id,
            'command_type' => 'unlock',
            'status' => 'pending',
            'created_by' => Auth::id(),
        ]);

        return redirect()->back()
            ->with('success', 'PC ' . $computer->pc_name . ' berhasil dibuka!');
    }

    /**
     * Send screenshot_request command — client akan capture & upload layarnya.
     * Returns JSON karena dipanggil via AJAX dari dashboard.
     */
    public function requestScreenshot(Computer $computer)
    {
        PcCommand::create([
            'computer_id' => $computer->id,
            'command_type' => 'screenshot_request',
            'status' => 'pending',
            'created_by' => Auth::id(),
        ]);

        return response()->json([
            'status'   => 'ok',
            'message'  => 'Screenshot request dikirim ke ' . $computer->pc_name,
            'pc_name'  => $computer->pc_name,
        ]);
    }

    // ==================================================================
    // Network Scanner — Deteksi & Register PC Baru
    // ==================================================================

    /**
     * Show the Network Scanner page.
     * Displays server IP and all discovered (unregistered) PCs.
     */
    public function scanner()
    {
        // Get server IP address
        $serverIp = request()->server('SERVER_ADDR', '127.0.0.1');
        // Fallback: try to get from hostname
        if ($serverIp === '127.0.0.1' || $serverIp === '::1') {
            $serverIp = gethostbyname(gethostname());
        }

        $discoveredPcs = Computer::where('status', 'unregistered')
            ->orderBy('last_heartbeat', 'desc')
            ->get();

        $registeredCount = Computer::where('status', '!=', 'unregistered')->count();

        return view('computers.scanner', compact('serverIp', 'discoveredPcs', 'registeredCount'));
    }

    /**
     * Get discovered PCs as JSON (for AJAX polling on scanner page).
     */
    public function scanNetwork()
    {
        $discovered = Computer::where('status', 'unregistered')
            ->orderBy('last_heartbeat', 'desc')
            ->get()
            ->map(function ($pc) {
                return [
                    'id'             => $pc->id,
                    'pc_name'        => $pc->pc_name,
                    'ip_address'     => $pc->ip_address,
                    'mac_address'    => $pc->mac_address,
                    'last_heartbeat' => $pc->last_heartbeat?->diffForHumans(),
                    'is_online'      => $pc->isOnline(),
                ];
            });

        return response()->json([
            'status'     => 'ok',
            'discovered' => $discovered,
            'count'      => $discovered->count(),
        ]);
    }

    /**
     * Register a discovered PC — ubah status dari 'unregistered' ke 'available'.
     */
    public function registerFromScanner(Request $request)
    {
        $request->validate([
            'computer_id' => 'required|exists:computers,id',
            'pc_name'     => 'nullable|string|max:255',
        ]);

        $computer = Computer::findOrFail($request->computer_id);

        if ($computer->status !== 'unregistered') {
            return response()->json([
                'status'  => 'error',
                'message' => 'PC sudah terdaftar.',
            ], 422);
        }

        // Allow renaming during registration
        if ($request->filled('pc_name') && $request->pc_name !== $computer->pc_name) {
            // Check unique
            $exists = Computer::where('pc_name', $request->pc_name)
                ->where('id', '!=', $computer->id)
                ->exists();
            if ($exists) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Nama PC sudah digunakan.',
                ], 422);
            }
            $computer->pc_name = $request->pc_name;
        }

        $computer->status = 'available';
        $computer->save();

        return response()->json([
            'status'  => 'ok',
            'message' => $computer->pc_name . ' berhasil didaftarkan!',
            'pc'      => [
                'id'         => $computer->id,
                'pc_name'    => $computer->pc_name,
                'ip_address' => $computer->ip_address,
                'status'     => $computer->status,
            ],
        ]);
    }
}
