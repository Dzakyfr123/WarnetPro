<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-white">Dashboard</h2>
                <p class="text-sm text-gray-400 mt-1">Selamat datang, {{ Auth::user()->name }}!</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('sessions.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-cyan-500 to-blue-600 text-white text-sm font-semibold rounded-xl hover:from-cyan-400 hover:to-blue-500 transition-all duration-200 shadow-lg shadow-cyan-500/25">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Mulai Sesi
                </a>
                <a href="{{ route('bookings.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-800 text-gray-300 text-sm font-semibold rounded-xl hover:bg-gray-700 hover:text-white transition-all duration-200 border border-gray-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    Buat Booking
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Stats Cards -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8" id="stats-cards">
                <!-- Available -->
                <div class="stats-card bg-gray-900/50 backdrop-blur-sm border border-gray-800/50 rounded-2xl p-5 relative overflow-hidden group hover:border-emerald-500/30 transition-all duration-300">
                    <div class="absolute top-0 right-0 w-20 h-20 bg-emerald-500/5 rounded-full -mr-6 -mt-6 group-hover:bg-emerald-500/10 transition-all"></div>
                    <div class="flex items-center justify-between relative">
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Available</p>
                            <p class="text-3xl font-bold text-emerald-400 mt-1" id="stat-available">{{ $stats['available'] }}</p>
                        </div>
                        <div class="w-12 h-12 bg-emerald-500/10 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </div>
                    </div>
                </div>

                <!-- Used -->
                <div class="stats-card bg-gray-900/50 backdrop-blur-sm border border-gray-800/50 rounded-2xl p-5 relative overflow-hidden group hover:border-blue-500/30 transition-all duration-300">
                    <div class="absolute top-0 right-0 w-20 h-20 bg-blue-500/5 rounded-full -mr-6 -mt-6 group-hover:bg-blue-500/10 transition-all"></div>
                    <div class="flex items-center justify-between relative">
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Digunakan</p>
                            <p class="text-3xl font-bold text-blue-400 mt-1" id="stat-used">{{ $stats['used'] }}</p>
                        </div>
                        <div class="w-12 h-12 bg-blue-500/10 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </div>
                    </div>
                </div>

                <!-- Booking -->
                <div class="stats-card bg-gray-900/50 backdrop-blur-sm border border-gray-800/50 rounded-2xl p-5 relative overflow-hidden group hover:border-amber-500/30 transition-all duration-300">
                    <div class="absolute top-0 right-0 w-20 h-20 bg-amber-500/5 rounded-full -mr-6 -mt-6 group-hover:bg-amber-500/10 transition-all"></div>
                    <div class="flex items-center justify-between relative">
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Booking</p>
                            <p class="text-3xl font-bold text-amber-400 mt-1" id="stat-booking">{{ $stats['booking'] }}</p>
                        </div>
                        <div class="w-12 h-12 bg-amber-500/10 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                    </div>
                </div>

                <!-- Offline -->
                <div class="stats-card bg-gray-900/50 backdrop-blur-sm border border-gray-800/50 rounded-2xl p-5 relative overflow-hidden group hover:border-gray-500/30 transition-all duration-300">
                    <div class="absolute top-0 right-0 w-20 h-20 bg-gray-500/5 rounded-full -mr-6 -mt-6 group-hover:bg-gray-500/10 transition-all"></div>
                    <div class="flex items-center justify-between relative">
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Offline</p>
                            <p class="text-3xl font-bold text-gray-400 mt-1" id="stat-offline">{{ $stats['offline'] }}</p>
                        </div>
                        <div class="w-12 h-12 bg-gray-500/10 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                        </div>
                    </div>
                </div>
            </div>



            <!-- PC Grid Overview -->
            <div class="mb-8">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-white">Status PC Realtime</h3>
                    <div class="flex items-center gap-2 text-xs text-gray-500">
                        <span class="inline-flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span> Available</span>
                        <span class="inline-flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-blue-400 animate-pulse"></span> Used</span>
                        <span class="inline-flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span> Booking</span>
                        <span class="inline-flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-gray-600"></span> Offline</span>
                    </div>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3" id="pc-grid">
                    @foreach($computers as $computer)
                        <div class="pc-card rounded-xl border p-4 transition-all duration-300 relative group
                            @if($computer->status === 'available') bg-emerald-500/5 border-emerald-500/20 hover:border-emerald-500/40
                            @elseif($computer->status === 'used') bg-blue-500/5 border-blue-500/20 hover:border-blue-500/40
                            @elseif($computer->status === 'booking') bg-amber-500/5 border-amber-500/20 hover:border-amber-500/40
                            @else bg-gray-800/50 border-gray-700/50
                            @endif"
                            data-pc-id="{{ $computer->id }}">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-bold text-white">{{ $computer->pc_name }}</span>
                                <div class="flex items-center gap-1.5">
                                    @if($computer->isOnline())
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-400" title="Client Online"></span>
                                    @endif
                                    <span class="w-2.5 h-2.5 rounded-full
                                        @if($computer->status === 'available') bg-emerald-400 animate-pulse
                                        @elseif($computer->status === 'used') bg-blue-400 animate-pulse
                                        @elseif($computer->status === 'booking') bg-amber-400 animate-pulse
                                        @else bg-gray-600
                                        @endif"></span>
                                </div>
                            </div>
                            <div class="text-xs
                                @if($computer->status === 'available') text-emerald-400
                                @elseif($computer->status === 'used') text-blue-400
                                @elseif($computer->status === 'booking') text-amber-400
                                @else text-gray-500
                                @endif">
                                {{ ucfirst($computer->status) }}
                            </div>
                            @if($computer->activeSession)
                                <div class="mt-2 pt-2 border-t border-gray-700/50">
                                    <div class="text-xs text-gray-400">{{ $computer->activeSession->display_name }}</div>
                                    <div class="text-sm font-mono font-bold text-blue-400 mt-1 session-timer" data-remaining="{{ $computer->activeSession->getRealRemainingSeconds() }}">
                                        --:--
                                    </div>
                                    <form action="{{ route('sessions.addTime', $computer->activeSession->id) }}" method="POST" class="flex items-center gap-1 mt-2">
                                        @csrf
                                        <input type="number" name="extra_minutes" value="30" min="1" max="720" class="w-14 bg-gray-800 border border-gray-700 text-gray-300 text-xs rounded-lg px-1.5 py-1 focus:ring-cyan-500 focus:border-cyan-500">
                                        <button type="submit" class="inline-flex items-center justify-center w-8 h-7 bg-cyan-500/10 text-cyan-400 text-xs font-semibold rounded-lg hover:bg-cyan-500/20 transition-all border border-cyan-500/20" title="Tambah Waktu">
                                            +
                                        </button>
                                    </form>
                                </div>
                            @elseif($computer->activeBooking)
                                <div class="mt-2 pt-2 border-t border-gray-700/50">
                                    <div class="text-xs text-gray-400">{{ $computer->activeBooking->customer_name }}</div>
                                    <div class="text-xs text-amber-400 mt-1">Menunggu</div>
                                </div>
                            @endif
                            {{-- Shutdown/Restart buttons (shown on hover) --}}
                            @if($computer->isOnline())
                                <div class="absolute top-1 right-1 hidden group-hover:flex items-center gap-1">
                                    <form action="{{ route('computers.restart', $computer) }}" method="POST" onsubmit="return confirm('Restart {{ $computer->pc_name }}?')">
                                        @csrf
                                        <button type="submit" class="w-6 h-6 bg-amber-500/20 rounded-md flex items-center justify-center text-amber-400 hover:bg-amber-500/30 transition-all" title="Restart">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                        </button>
                                    </form>
                                    <form action="{{ route('computers.shutdown', $computer) }}" method="POST" onsubmit="return confirm('Shutdown {{ $computer->pc_name }}?')">
                                        @csrf
                                        <button type="submit" class="w-6 h-6 bg-red-500/20 rounded-md flex items-center justify-center text-red-400 hover:bg-red-500/30 transition-all" title="Shutdown">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636a9 9 0 11-12.728 0M12 3v9"/></svg>
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Active Sessions -->
                <div class="bg-gray-900/50 backdrop-blur-sm border border-gray-800/50 rounded-2xl overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-800/50 flex items-center justify-between">
                        <h3 class="text-base font-semibold text-white flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-blue-400 animate-pulse"></span>
                            Sesi Aktif
                        </h3>
                        <span class="text-xs text-gray-500" id="session-count">{{ $activeSessions->count() }} aktif</span>
                    </div>
                    <div class="divide-y divide-gray-800/50" id="active-sessions-list">
                        @forelse($activeSessions as $session)
                            <div class="px-6 py-4 session-item hover:bg-gray-800/30 transition-all" data-session-id="{{ $session->id }}">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-blue-500/10 rounded-xl flex items-center justify-center">
                                            <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                        </div>
                                        <div>
                                            <div class="text-sm font-semibold text-white">{{ $session->computer->pc_name }}</div>
                                            <div class="text-xs text-gray-400">{{ $session->display_name }}</div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-lg font-mono font-bold session-timer
                                            {{ $session->real_remaining_seconds <= 60 ? 'text-red-400 animate-pulse' : ($session->real_remaining_seconds <= 300 ? 'text-amber-400' : 'text-cyan-400') }}"
                                            data-remaining="{{ $session->real_remaining_seconds }}">
                                            --:--
                                        </div>
                                        <div class="text-xs text-gray-500">{{ $session->duration_minutes }} menit</div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 mt-3">
                                    <form action="{{ route('sessions.addTime', $session->id) }}" method="POST" class="flex items-center gap-1">
                                        @csrf
                                        <input type="number" name="extra_minutes" value="30" min="1" max="720" class="w-16 bg-gray-800 border border-gray-700 text-gray-300 text-xs rounded-lg px-2 py-1.5 focus:ring-cyan-500 focus:border-cyan-500">
                                        <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 bg-cyan-500/10 text-cyan-400 text-xs font-medium rounded-lg hover:bg-cyan-500/20 transition-all border border-cyan-500/20">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                            Tambah
                                        </button>
                                    </form>
                                    <form action="{{ route('sessions.endSession', $session->id) }}" method="POST" onsubmit="return confirm('Akhiri sesi ini?')">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 bg-red-500/10 text-red-400 text-xs font-medium rounded-lg hover:bg-red-500/20 transition-all border border-red-500/20">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"/></svg>
                                            Akhiri
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="px-6 py-12 text-center">
                                <svg class="w-12 h-12 text-gray-700 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <p class="text-gray-500 text-sm">Belum ada sesi aktif</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Recent Bookings -->
                <div class="bg-gray-900/50 backdrop-blur-sm border border-gray-800/50 rounded-2xl overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-800/50 flex items-center justify-between">
                        <h3 class="text-base font-semibold text-white flex items-center gap-2">
                            <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            Booking Terbaru
                        </h3>
                        <a href="{{ route('bookings.index') }}" class="text-xs text-cyan-400 hover:text-cyan-300">Lihat semua →</a>
                    </div>
                    <div class="divide-y divide-gray-800/50">
                        @forelse($recentBookings as $booking)
                            <div class="px-6 py-4 hover:bg-gray-800/30 transition-all">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl flex items-center justify-center
                                            @if($booking->status === 'active') bg-amber-500/10
                                            @elseif($booking->status === 'finished') bg-emerald-500/10
                                            @else bg-red-500/10
                                            @endif">
                                            @if($booking->status === 'active')
                                                <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            @elseif($booking->status === 'finished')
                                                <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            @else
                                                <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="text-sm font-semibold text-white">{{ $booking->customer_name }}</div>
                                            <div class="text-xs text-gray-400">{{ $booking->computer->pc_name }} · {{ $booking->booking_start->format('d M H:i') }}</div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="px-2 py-1 rounded-lg text-xs font-medium
                                            @if($booking->status === 'active') bg-amber-500/10 text-amber-400 border border-amber-500/20
                                            @elseif($booking->status === 'finished') bg-emerald-500/10 text-emerald-400 border border-emerald-500/20
                                            @else bg-red-500/10 text-red-400 border border-red-500/20
                                            @endif">
                                            {{ ucfirst($booking->status) }}
                                        </span>
                                        @if($booking->status === 'active')
                                            <form action="{{ route('bookings.startSession', $booking->id) }}" method="POST" class="inline-flex items-center gap-1">
                                                @csrf
                                                <input type="hidden" name="duration_minutes" value="60">
                                                <button type="submit" class="px-2 py-1 bg-blue-500/10 text-blue-400 text-xs font-medium rounded-lg hover:bg-blue-500/20 transition-all border border-blue-500/20" title="Start sesi 60 menit">
                                                    Start
                                                </button>
                                            </form>
                                            <form action="{{ route('bookings.cancel', $booking->id) }}" method="POST" onsubmit="return confirm('Batalkan booking?')">
                                                @csrf
                                                <button type="submit" class="px-2 py-1 bg-red-500/10 text-red-400 text-xs font-medium rounded-lg hover:bg-red-500/20 transition-all border border-red-500/20">
                                                    Batal
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="px-6 py-12 text-center">
                                <svg class="w-12 h-12 text-gray-700 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <p class="text-gray-500 text-sm">Belum ada booking</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // ---- Countdown Timer Logic ----
        function formatTime(totalSeconds) {
            if (totalSeconds <= 0) return '00:00';
            const hours = Math.floor(totalSeconds / 3600);
            const minutes = Math.floor((totalSeconds % 3600) / 60);
            const seconds = totalSeconds % 60;
            if (hours > 0) {
                return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            }
            return `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        }

        // Initialize all timers
        document.querySelectorAll('.session-timer').forEach(el => {
            let remaining = parseInt(el.dataset.remaining) || 0;
            el.textContent = formatTime(remaining);

            setInterval(() => {
                remaining = Math.max(0, remaining - 1);
                el.textContent = formatTime(remaining);

                // Warning colors
                el.classList.remove('text-cyan-400', 'text-amber-400', 'text-red-400', 'animate-pulse');
                if (remaining <= 60) {
                    el.classList.add('text-red-400', 'animate-pulse');
                } else if (remaining <= 300) {
                    el.classList.add('text-amber-400');
                } else {
                    el.classList.add('text-cyan-400');
                }

                // Auto-reload when timer hits 0
                if (remaining === 0) {
                    setTimeout(() => location.reload(), 2000);
                }
            }, 1000);
        });

        // ---- Auto-refresh stats every 10 seconds ----
        setInterval(async () => {
            try {
                const response = await fetch('{{ route("api.computerStats") }}');
                const data = await response.json();

                // Update stat numbers
                document.getElementById('stat-available').textContent = data.stats.available;
                document.getElementById('stat-used').textContent = data.stats.used;
                document.getElementById('stat-booking').textContent = data.stats.booking;
                document.getElementById('stat-offline').textContent = data.stats.offline;
            } catch (e) {
                console.error('Failed to fetch stats:', e);
            }
        }, 10000);

        // Auto-dismiss flash messages
        setTimeout(() => {
            document.querySelectorAll('.flash-message').forEach(el => {
                el.style.transition = 'opacity 0.5s ease';
                el.style.opacity = '0';
                setTimeout(() => el.remove(), 500);
            });
        }, 4000);
    </script>
    @endpush
</x-app-layout>
