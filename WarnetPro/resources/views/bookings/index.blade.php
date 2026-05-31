<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-white">Booking</h2>
                <p class="text-sm text-gray-400 mt-1">Kelola booking PC warnet</p>
            </div>
            <a href="{{ route('bookings.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-cyan-500 to-blue-600 text-white text-sm font-semibold rounded-xl hover:from-cyan-400 hover:to-blue-500 transition-all duration-200 shadow-lg shadow-cyan-500/25">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                Buat Booking
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Filter Tabs -->
            <div class="flex items-center gap-2 mb-6">
                <a href="{{ route('bookings.index', ['status' => 'all']) }}"
                   class="px-4 py-2 rounded-xl text-sm font-medium transition-all {{ $status === 'all' ? 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20' : 'bg-gray-800 text-gray-400 border border-gray-700 hover:text-white' }}">
                    Semua
                </a>
                <a href="{{ route('bookings.index', ['status' => 'active']) }}"
                   class="px-4 py-2 rounded-xl text-sm font-medium transition-all {{ $status === 'active' ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20' : 'bg-gray-800 text-gray-400 border border-gray-700 hover:text-white' }}">
                    Aktif
                </a>
                <a href="{{ route('bookings.index', ['status' => 'finished']) }}"
                   class="px-4 py-2 rounded-xl text-sm font-medium transition-all {{ $status === 'finished' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-gray-800 text-gray-400 border border-gray-700 hover:text-white' }}">
                    Selesai
                </a>
                <a href="{{ route('bookings.index', ['status' => 'cancelled']) }}"
                   class="px-4 py-2 rounded-xl text-sm font-medium transition-all {{ $status === 'cancelled' ? 'bg-red-500/10 text-red-400 border border-red-500/20' : 'bg-gray-800 text-gray-400 border border-gray-700 hover:text-white' }}">
                    Dibatalkan
                </a>
            </div>

            <!-- Bookings Table -->
            <div class="bg-gray-900/50 backdrop-blur-sm border border-gray-800/50 rounded-2xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-800/50">
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PC</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu Booking</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Operator</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-800/50">
                            @forelse($bookings as $booking)
                                <tr class="hover:bg-gray-800/30 transition-all">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-cyan-500/20 to-blue-500/20 flex items-center justify-center">
                                                <span class="text-sm font-bold text-cyan-400">{{ strtoupper(substr($booking->customer_name, 0, 1)) }}</span>
                                            </div>
                                            <span class="text-sm font-medium text-white">{{ $booking->customer_name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-sm font-medium text-gray-300">{{ $booking->computer->pc_name }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-300">{{ $booking->booking_start->format('d M Y') }}</div>
                                        <div class="text-xs text-gray-500">{{ $booking->booking_start->format('H:i') }} - {{ $booking->booking_end->format('H:i') }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-sm text-gray-400">{{ $booking->creator->name }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2.5 py-1 rounded-lg text-xs font-medium
                                            @if($booking->status === 'active') bg-amber-500/10 text-amber-400 border border-amber-500/20
                                            @elseif($booking->status === 'finished') bg-emerald-500/10 text-emerald-400 border border-emerald-500/20
                                            @else bg-red-500/10 text-red-400 border border-red-500/20
                                            @endif">
                                            {{ ucfirst($booking->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        @if($booking->status === 'active')
                                            <div class="flex items-center justify-end gap-2">
                                                <form action="{{ route('bookings.startSession', $booking) }}" method="POST" x-data="{ showDuration: false }" class="relative">
                                                    @csrf
                                                    <div x-show="showDuration" x-cloak class="absolute right-0 bottom-full mb-2 bg-gray-800 border border-gray-700 rounded-xl p-3 shadow-2xl z-10 w-48">
                                                        <label class="text-xs text-gray-400 mb-1 block">Durasi (menit)</label>
                                                        <input type="number" name="duration_minutes" value="60" min="1" max="720"
                                                               class="w-full bg-gray-900 border border-gray-600 text-white text-sm rounded-lg px-3 py-2 mb-2 focus:ring-cyan-500">
                                                        <button type="submit" class="w-full px-3 py-2 bg-blue-500 text-white text-xs font-medium rounded-lg hover:bg-blue-400 transition-all">
                                                            Mulai Sesi
                                                        </button>
                                                    </div>
                                                    <button type="button" @click="showDuration = !showDuration"
                                                            class="px-3 py-1.5 bg-blue-500/10 text-blue-400 text-xs font-medium rounded-lg hover:bg-blue-500/20 transition-all border border-blue-500/20">
                                                        Start
                                                    </button>
                                                </form>
                                                <form action="{{ route('bookings.cancel', $booking) }}" method="POST" onsubmit="return confirm('Batalkan booking ini?')">
                                                    @csrf
                                                    <button type="submit" class="px-3 py-1.5 bg-red-500/10 text-red-400 text-xs font-medium rounded-lg hover:bg-red-500/20 transition-all border border-red-500/20">
                                                        Batal
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center">
                                        <svg class="w-12 h-12 text-gray-700 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        <p class="text-gray-500 text-sm">Belum ada booking</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($bookings->hasPages())
                    <div class="px-6 py-4 border-t border-gray-800/50">
                        {{ $bookings->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
