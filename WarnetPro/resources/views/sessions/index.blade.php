<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-white">History Penggunaan</h2>
                <p class="text-sm text-gray-400 mt-1">Riwayat semua sesi bermain</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Filter Tabs -->
            <div class="flex items-center gap-2 mb-6">
                <a href="{{ route('sessions.index', ['status' => 'all']) }}"
                   class="px-4 py-2 rounded-xl text-sm font-medium transition-all {{ $status === 'all' ? 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20' : 'bg-gray-800 text-gray-400 border border-gray-700 hover:text-white' }}">
                    Semua
                </a>
                <a href="{{ route('sessions.index', ['status' => 'playing']) }}"
                   class="px-4 py-2 rounded-xl text-sm font-medium transition-all {{ $status === 'playing' ? 'bg-blue-500/10 text-blue-400 border border-blue-500/20' : 'bg-gray-800 text-gray-400 border border-gray-700 hover:text-white' }}">
                    🔵 Bermain
                </a>
                <a href="{{ route('sessions.index', ['status' => 'finished']) }}"
                   class="px-4 py-2 rounded-xl text-sm font-medium transition-all {{ $status === 'finished' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-gray-800 text-gray-400 border border-gray-700 hover:text-white' }}">
                    ✅ Selesai
                </a>
            </div>

            <!-- Sessions Table -->
            <div class="bg-gray-900/50 backdrop-blur-sm border border-gray-800/50 rounded-2xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-800/50">
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PC</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mulai</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Selesai</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Durasi</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sisa Waktu</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-800/50">
                            @forelse($sessions as $session)
                                <tr class="hover:bg-gray-800/30 transition-all">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-cyan-500/20 to-blue-500/20 flex items-center justify-center">
                                                <span class="text-sm font-bold text-cyan-400">{{ strtoupper(substr($session->customer_name, 0, 1)) }}</span>
                                            </div>
                                            <span class="text-sm font-medium text-white">{{ $session->customer_name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-sm font-medium text-gray-300">{{ $session->computer->pc_name }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-300">{{ $session->start_time->format('d M Y') }}</div>
                                        <div class="text-xs text-gray-500">{{ $session->start_time->format('H:i:s') }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($session->end_time)
                                            <div class="text-sm text-gray-300">{{ $session->end_time->format('d M Y') }}</div>
                                            <div class="text-xs text-gray-500">{{ $session->end_time->format('H:i:s') }}</div>
                                        @else
                                            <span class="text-xs text-gray-600">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-sm font-medium text-gray-300">{{ $session->duration_minutes }} menit</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($session->status === 'playing')
                                            <span class="text-sm font-mono font-bold session-timer text-cyan-400" data-remaining="{{ $session->getRealRemainingSeconds() }}">--:--</span>
                                        @else
                                            <span class="text-sm text-gray-500">0 menit</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2.5 py-1 rounded-lg text-xs font-medium
                                            @if($session->status === 'playing') bg-blue-500/10 text-blue-400 border border-blue-500/20
                                            @else bg-emerald-500/10 text-emerald-400 border border-emerald-500/20
                                            @endif">
                                            {{ $session->status === 'playing' ? 'Bermain' : 'Selesai' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        @if($session->status === 'playing')
                                            <div class="flex items-center justify-end gap-2">
                                                <form action="{{ route('sessions.addTime', $session->id) }}" method="POST" class="flex items-center gap-1">
                                                    @csrf
                                                    <input type="number" name="extra_minutes" value="30" min="1" max="720"
                                                           class="w-16 bg-gray-800 border border-gray-700 text-gray-300 text-xs rounded-lg px-2 py-1.5 focus:ring-cyan-500 focus:border-cyan-500">
                                                    <button type="submit" class="px-3 py-1.5 bg-cyan-500/10 text-cyan-400 text-xs font-medium rounded-lg hover:bg-cyan-500/20 transition-all border border-cyan-500/20">
                                                        +Waktu
                                                    </button>
                                                </form>
                                                <form action="{{ route('sessions.endSession', $session->id) }}" method="POST" onsubmit="return confirm('Akhiri sesi?')">
                                                    @csrf
                                                    <button type="submit" class="px-3 py-1.5 bg-red-500/10 text-red-400 text-xs font-medium rounded-lg hover:bg-red-500/20 transition-all border border-red-500/20">
                                                        Akhiri
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-12 text-center">
                                        <svg class="w-12 h-12 text-gray-700 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <p class="text-gray-500 text-sm">Belum ada riwayat sesi</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($sessions->hasPages())
                    <div class="px-6 py-4 border-t border-gray-800/50">
                        {{ $sessions->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function formatTime(totalSeconds) {
            if (totalSeconds <= 0) return '00:00';
            const h = Math.floor(totalSeconds / 3600);
            const m = Math.floor((totalSeconds % 3600) / 60);
            const s = totalSeconds % 60;
            if (h > 0) return `${h.toString().padStart(2,'0')}:${m.toString().padStart(2,'0')}:${s.toString().padStart(2,'0')}`;
            return `${m.toString().padStart(2,'0')}:${s.toString().padStart(2,'0')}`;
        }
        document.querySelectorAll('.session-timer').forEach(el => {
            let rem = parseInt(el.dataset.remaining) || 0;
            el.textContent = formatTime(rem);
            setInterval(() => {
                rem = Math.max(0, rem - 1);
                el.textContent = formatTime(rem);
                el.classList.remove('text-cyan-400', 'text-amber-400', 'text-red-400', 'animate-pulse');
                if (rem <= 60) el.classList.add('text-red-400', 'animate-pulse');
                else if (rem <= 300) el.classList.add('text-amber-400');
                else el.classList.add('text-cyan-400');
                if (rem === 0) setTimeout(() => location.reload(), 2000);
            }, 1000);
        });
    </script>
    @endpush
</x-app-layout>
