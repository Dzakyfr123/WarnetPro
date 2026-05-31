<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('dashboard') }}" class="w-10 h-10 bg-gray-800 rounded-xl flex items-center justify-center text-gray-400 hover:text-white hover:bg-gray-700 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <h2 class="text-2xl font-bold text-white">Mulai Sesi Baru</h2>
                <p class="text-sm text-gray-400 mt-1">Langsung mulai sesi bermain tanpa booking</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-gray-900/50 backdrop-blur-sm border border-gray-800/50 rounded-2xl p-6">
                <form action="{{ route('sessions.store') }}" method="POST">
                    @csrf

                    <div class="mb-6">
                        <label for="computer_id" class="block text-sm font-medium text-gray-300 mb-2">Pilih PC</label>
                        @if($availableComputers->isEmpty())
                            <div class="bg-amber-500/10 border border-amber-500/20 rounded-xl p-4 text-amber-400 text-sm">
                                ⚠️ Tidak ada PC tersedia saat ini
                            </div>
                        @else
                            <div class="grid grid-cols-3 sm:grid-cols-5 gap-2">
                                @foreach($availableComputers as $computer)
                                    <label class="cursor-pointer">
                                        <input type="radio" name="computer_id" value="{{ $computer->id }}" class="hidden peer" {{ old('computer_id') == $computer->id ? 'checked' : '' }} required>
                                        <div class="text-center px-3 py-3 bg-gray-800 border border-gray-700 rounded-xl text-sm font-medium text-gray-400 peer-checked:bg-cyan-500/10 peer-checked:border-cyan-500/30 peer-checked:text-cyan-400 hover:border-gray-600 hover:text-gray-300 transition-all">
                                            <svg class="w-5 h-5 mx-auto mb-1 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                            {{ $computer->pc_name }}
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        @endif
                        @error('computer_id')
                            <p class="text-red-400 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label for="duration_minutes" class="block text-sm font-medium text-gray-300 mb-2">Durasi Bermain</label>
                        <div class="grid grid-cols-4 gap-2 mb-3">
                            <button type="button" onclick="document.getElementById('duration_minutes').value=30" class="px-3 py-2 bg-gray-800 border border-gray-700 text-gray-400 text-sm font-medium rounded-xl hover:bg-gray-700 hover:text-white transition-all">30 min</button>
                            <button type="button" onclick="document.getElementById('duration_minutes').value=60" class="px-3 py-2 bg-gray-800 border border-gray-700 text-gray-400 text-sm font-medium rounded-xl hover:bg-gray-700 hover:text-white transition-all">1 jam</button>
                            <button type="button" onclick="document.getElementById('duration_minutes').value=120" class="px-3 py-2 bg-gray-800 border border-gray-700 text-gray-400 text-sm font-medium rounded-xl hover:bg-gray-700 hover:text-white transition-all">2 jam</button>
                            <button type="button" onclick="document.getElementById('duration_minutes').value=180" class="px-3 py-2 bg-gray-800 border border-gray-700 text-gray-400 text-sm font-medium rounded-xl hover:bg-gray-700 hover:text-white transition-all">3 jam</button>
                        </div>
                        <input type="number" name="duration_minutes" id="duration_minutes" value="{{ old('duration_minutes', 60) }}"
                               min="1" max="720" placeholder="Durasi dalam menit"
                               class="w-full bg-gray-800 border border-gray-700 text-white rounded-xl px-4 py-3 focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 placeholder-gray-500 transition-all"
                               required>
                        @error('duration_minutes')
                            <p class="text-red-400 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center gap-3">
                        <button type="submit" class="px-6 py-3 bg-gradient-to-r from-cyan-500 to-blue-600 text-white font-semibold rounded-xl hover:from-cyan-400 hover:to-blue-500 transition-all shadow-lg shadow-cyan-500/25"
                                {{ $availableComputers->isEmpty() ? 'disabled' : '' }}>
                            <span class="flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Mulai Bermain
                            </span>
                        </button>
                        <a href="{{ route('dashboard') }}" class="px-6 py-3 bg-gray-800 text-gray-300 font-semibold rounded-xl hover:bg-gray-700 hover:text-white transition-all border border-gray-700">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
