<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-white">Member</h2>
                <p class="text-sm text-gray-400 mt-1">Kelola akun member warnet</p>
            </div>
            <a href="{{ route('members.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-cyan-500 to-blue-600 text-white text-sm font-semibold rounded-xl hover:from-cyan-400 hover:to-blue-500 transition-all duration-200 shadow-lg shadow-cyan-500/25">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                Buat Member
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Search --}}
            <div class="mb-6">
                <form action="{{ route('members.index') }}" method="GET">
                    <div class="flex gap-3">
                        <input type="text" name="search" value="{{ $search }}" placeholder="Cari member..."
                               class="flex-1 bg-gray-800 border border-gray-700 text-white rounded-xl px-4 py-3 focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 placeholder-gray-500 transition-all">
                        <button type="submit" class="px-6 py-3 bg-gray-800 text-gray-300 font-semibold rounded-xl hover:bg-gray-700 hover:text-white transition-all border border-gray-700">
                            Cari
                        </button>
                    </div>
                </form>
            </div>

            {{-- Member List --}}
            <div class="bg-gray-900/50 backdrop-blur-sm border border-gray-800/50 rounded-2xl overflow-hidden">
                <div class="divide-y divide-gray-800/50">
                    @forelse($members as $member)
                        <a href="{{ route('members.show', $member) }}" class="block px-6 py-4 hover:bg-gray-800/30 transition-all group">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center text-white font-bold text-lg shadow-lg shadow-purple-500/25">
                                        {{ strtoupper(substr($member->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="text-sm font-semibold text-white group-hover:text-cyan-400 transition-colors">{{ $member->name }}</div>
                                        <div class="text-xs text-gray-500">@{{ $member->username }}</div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-4">
                                    <div class="text-right">
                                        <div class="text-sm font-bold {{ $member->remaining_minutes > 0 ? 'text-cyan-400' : 'text-gray-500' }}">
                                            {{ $member->formatted_remaining_time }}
                                        </div>
                                        <div class="text-xs text-gray-500">sisa waktu</div>
                                    </div>
                                    <span class="px-2 py-1 rounded-lg text-xs font-medium {{ $member->status === 'active' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-red-500/10 text-red-400 border border-red-500/20' }}">
                                        {{ ucfirst($member->status) }}
                                    </span>
                                    <svg class="w-4 h-4 text-gray-600 group-hover:text-gray-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="px-6 py-16 text-center">
                            <svg class="w-16 h-16 text-gray-700 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <p class="text-gray-500 text-base mb-2">Belum ada member</p>
                            <p class="text-gray-600 text-sm">Klik "Buat Member" untuk menambahkan member baru</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Pagination --}}
            <div class="mt-6">
                {{ $members->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
