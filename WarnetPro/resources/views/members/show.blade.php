<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('members.index') }}" class="w-10 h-10 bg-gray-800 rounded-xl flex items-center justify-center text-gray-400 hover:text-white hover:bg-gray-700 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <h2 class="text-2xl font-bold text-white">{{ $member->name }}</h2>
                <p class="text-sm text-gray-400 mt-1">@{{ $member->username }} · Member sejak {{ $member->created_at->format('d M Y') }}</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Member Stats --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                {{-- Remaining Time --}}
                <div class="bg-gray-900/50 backdrop-blur-sm border border-gray-800/50 rounded-2xl p-5">
                    <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">Sisa Waktu</div>
                    <div class="text-2xl font-bold text-cyan-400 mt-1">{{ $member->formatted_remaining_time }}</div>
                </div>
                {{-- Total Usage --}}
                <div class="bg-gray-900/50 backdrop-blur-sm border border-gray-800/50 rounded-2xl p-5">
                    <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Pemakaian</div>
                    <div class="text-2xl font-bold text-blue-400 mt-1">
                        {{ intdiv($member->total_usage_minutes, 60) }} jam {{ $member->total_usage_minutes % 60 }} menit
                    </div>
                </div>
                {{-- Status --}}
                <div class="bg-gray-900/50 backdrop-blur-sm border border-gray-800/50 rounded-2xl p-5">
                    <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">Status</div>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="w-2.5 h-2.5 rounded-full {{ $member->status === 'active' ? 'bg-emerald-400 animate-pulse' : 'bg-red-400' }}"></span>
                        <span class="text-2xl font-bold {{ $member->status === 'active' ? 'text-emerald-400' : 'text-red-400' }}">
                            {{ ucfirst($member->status) }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="grid grid-cols-1 md:grid-cols-1 gap-4 mb-6">
                {{-- Account Actions --}}
                <div class="bg-gray-900/50 backdrop-blur-sm border border-gray-800/50 rounded-2xl p-6">
                    <h3 class="text-base font-semibold text-white mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Aksi Akun
                    </h3>
                    <div class="space-y-3">
                        {{-- Toggle Status --}}
                        <form action="{{ route('members.toggleStatus', $member) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-3 rounded-xl transition-all border {{ $member->status === 'active' ? 'bg-red-500/5 border-red-500/20 text-red-400 hover:bg-red-500/10' : 'bg-emerald-500/5 border-emerald-500/20 text-emerald-400 hover:bg-emerald-500/10' }}">
                                <div class="text-sm font-medium">{{ $member->status === 'active' ? 'Suspend Akun' : 'Aktifkan Akun' }}</div>
                                <div class="text-xs opacity-70 mt-0.5">{{ $member->status === 'active' ? 'Member tidak bisa login' : 'Member bisa login kembali' }}</div>
                            </button>
                        </form>

                        {{-- Reset Password --}}
                        <div x-data="{ open: false }">
                            <button @click="open = !open" class="w-full text-left px-4 py-3 rounded-xl transition-all border bg-amber-500/5 border-amber-500/20 text-amber-400 hover:bg-amber-500/10">
                                <div class="text-sm font-medium">Reset Password</div>
                                <div class="text-xs opacity-70 mt-0.5">Ganti password member</div>
                            </button>
                            <div x-show="open" x-transition class="mt-2 p-4 bg-gray-800 rounded-xl border border-gray-700">
                                <form action="{{ route('members.resetPassword', $member) }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <input type="password" name="password" placeholder="Password baru (min 4 karakter)"
                                               class="w-full bg-gray-900 border border-gray-700 text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 placeholder-gray-500" required>
                                    </div>
                                    <div class="mb-3">
                                        <input type="password" name="password_confirmation" placeholder="Konfirmasi password"
                                               class="w-full bg-gray-900 border border-gray-700 text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 placeholder-gray-500" required>
                                    </div>
                                    <button type="submit" class="px-4 py-2 bg-amber-500/20 text-amber-400 text-sm font-medium rounded-lg hover:bg-amber-500/30 transition-all border border-amber-500/30">
                                        Simpan Password
                                    </button>
                                </form>
                            </div>
                        </div>

                        {{-- Delete --}}
                        <form action="{{ route('members.destroy', $member) }}" method="POST" onsubmit="return confirm('Hapus member {{ $member->name }}? Data tidak bisa dikembalikan!')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full text-left px-4 py-3 rounded-xl transition-all border bg-gray-800/50 border-gray-700 text-gray-500 hover:bg-red-500/10 hover:text-red-400 hover:border-red-500/20">
                                <div class="text-sm font-medium">Hapus Member</div>
                                <div class="text-xs opacity-70 mt-0.5">Hapus permanen akun member</div>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Session History --}}
            <div class="bg-gray-900/50 backdrop-blur-sm border border-gray-800/50 rounded-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-800/50">
                    <h3 class="text-base font-semibold text-white flex items-center gap-2">
                        <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Riwayat Pemakaian
                    </h3>
                </div>
                <div class="divide-y divide-gray-800/50">
                    @forelse($sessions as $session)
                        <div class="px-6 py-4 hover:bg-gray-800/30 transition-all">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl flex items-center justify-center {{ $session->status === 'playing' ? 'bg-blue-500/10' : 'bg-gray-800' }}">
                                        <svg class="w-5 h-5 {{ $session->status === 'playing' ? 'text-blue-400' : 'text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    </div>
                                    <div>
                                        <div class="text-sm font-semibold text-white">{{ $session->computer->pc_name }}</div>
                                        <div class="text-xs text-gray-500">{{ $session->start_time->format('d M Y · H:i') }}</div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm font-medium {{ $session->status === 'playing' ? 'text-blue-400' : 'text-gray-400' }}">
                                        {{ $session->duration_minutes }} menit
                                    </div>
                                    <span class="text-xs px-2 py-0.5 rounded {{ $session->status === 'playing' ? 'bg-blue-500/10 text-blue-400' : 'bg-gray-800 text-gray-500' }}">
                                        {{ ucfirst($session->status) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-12 text-center">
                            <p class="text-gray-500 text-sm">Belum ada riwayat pemakaian</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="mt-6">
                {{ $sessions->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
