<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Computer;
use App\Models\PlaySession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    /**
     * Display a listing of bookings.
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');

        $query = Booking::with(['computer', 'creator'])
            ->orderBy('created_at', 'desc');

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        $bookings = $query->paginate(15);

        return view('bookings.index', compact('bookings', 'status'));
    }

    /**
     * Show the form for creating a new booking.
     */
    public function create()
    {
        $availableComputers = Computer::where('status', 'available')
            ->orderBy('pc_name')
            ->get();

        return view('bookings.create', compact('availableComputers'));
    }

    /**
     * Store a newly created booking.
     */
    public function store(Request $request)
    {
        $request->validate([
            'computer_id' => 'required|exists:computers,id',
            'customer_name' => 'required|string|max:255',
            'booking_start' => 'required|date',
            'booking_end' => 'required|date|after:booking_start',
        ]);

        // Check if computer is available
        $computer = Computer::findOrFail($request->computer_id);
        if ($computer->status !== 'available') {
            return redirect()->back()
                ->withInput()
                ->with('error', 'PC tidak tersedia untuk booking!');
        }

        Booking::create([
            'computer_id' => $request->computer_id,
            'customer_name' => $request->customer_name,
            'booking_start' => $request->booking_start,
            'booking_end' => $request->booking_end,
            'status' => 'active',
            'created_by' => Auth::id(),
        ]);

        // Update computer status to booking
        $computer->update(['status' => 'booking']);

        return redirect()->route('bookings.index')
            ->with('success', 'Booking berhasil dibuat!');
    }

    /**
     * Cancel a booking.
     */
    public function cancel(Booking $booking)
    {
        if ($booking->status !== 'active') {
            return redirect()->back()
                ->with('error', 'Booking tidak dapat dibatalkan!');
        }

        $booking->update(['status' => 'cancelled']);

        // Set computer back to available
        $booking->computer->update(['status' => 'available']);

        return redirect()->back()
            ->with('success', 'Booking berhasil dibatalkan!');
    }

    /**
     * Start a play session from a booking.
     */
    public function startSession(Request $request, Booking $booking)
    {
        $request->validate([
            'duration_minutes' => 'required|integer|min:1|max:720',
        ]);

        if ($booking->status !== 'active') {
            return redirect()->back()
                ->with('error', 'Booking tidak aktif!');
        }

        $duration = (int) $request->duration_minutes;

        // Create play session
        PlaySession::create([
            'computer_id' => $booking->computer_id,
            'booking_id' => $booking->id,
            'customer_name' => $booking->customer_name,
            'start_time' => now(),
            'end_time' => now()->addMinutes($duration),
            'duration_minutes' => $duration,
            'remaining_minutes' => $duration,
            'status' => 'playing',
            'created_by' => Auth::id(),
        ]);

        // Update booking status
        $booking->update(['status' => 'finished']);

        // Update computer status to used
        $booking->computer->update(['status' => 'used']);

        return redirect()->route('dashboard')
            ->with('success', 'Sesi bermain dimulai!');
    }

    /**
     * Remove the specified booking.
     */
    public function destroy(Booking $booking)
    {
        if ($booking->status === 'active') {
            $booking->computer->update(['status' => 'available']);
        }

        $booking->delete();

        return redirect()->route('bookings.index')
            ->with('success', 'Booking berhasil dihapus!');
    }
}
