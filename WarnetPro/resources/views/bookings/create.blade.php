<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('bookings.index') }}" class="w-10 h-10 bg-gray-800 rounded-xl flex items-center justify-center text-gray-400 hover:text-white hover:bg-gray-700 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <h2 class="text-2xl font-bold text-white">Buat Booking Baru</h2>
                <p class="text-sm text-gray-400 mt-1">Reservasi PC untuk customer</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-gray-900/50 backdrop-blur-sm border border-gray-800/50 rounded-2xl p-6">
                <form action="{{ route('bookings.store') }}" method="POST">
                    @csrf

                    <div class="mb-6">
                        <label for="customer_name" class="block text-sm font-medium text-gray-300 mb-2">Nama Customer</label>
                        <input type="text" name="customer_name" id="customer_name" value="{{ old('customer_name') }}"
                               placeholder="Masukkan nama customer"
                               class="w-full bg-gray-800 border border-gray-700 text-white rounded-xl px-4 py-3 focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 placeholder-gray-500 transition-all"
                               required>
                        @error('customer_name')
                            <p class="text-red-400 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label for="computer_id" class="block text-sm font-medium text-gray-300 mb-2">Pilih PC</label>
                        @if($availableComputers->isEmpty())
                            <div class="bg-amber-500/10 border border-amber-500/20 rounded-xl p-4 text-amber-400 text-sm">
                                ⚠️ Tidak ada PC tersedia saat ini
                            </div>
                        @else
                            <select name="computer_id" id="computer_id"
                                    class="w-full bg-gray-800 border border-gray-700 text-white rounded-xl px-4 py-3 focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition-all"
                                    required>
                                <option value="">-- Pilih PC --</option>
                                @foreach($availableComputers as $computer)
                                    <option value="{{ $computer->id }}" {{ old('computer_id') == $computer->id ? 'selected' : '' }}>
                                        {{ $computer->pc_name }}
                                    </option>
                                @endforeach
                            </select>
                        @endif
                        @error('computer_id')
                            <p class="text-red-400 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div>
                            <label for="booking_start" class="block text-sm font-medium text-gray-300 mb-2">Mulai Booking</label>
                            <input type="datetime-local" name="booking_start" id="booking_start" value="{{ old('booking_start') }}"
                                   class="w-full bg-gray-800 border border-gray-700 text-white rounded-xl px-4 py-3 focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition-all"
                                   required>
                            @error('booking_start')
                                <p class="text-red-400 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="booking_end" class="block text-sm font-medium text-gray-300 mb-2">Akhir Booking</label>
                            <input type="datetime-local" name="booking_end" id="booking_end" value="{{ old('booking_end') }}"
                                   class="w-full bg-gray-800 border border-gray-700 text-white rounded-xl px-4 py-3 focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition-all"
                                   required>
                            @error('booking_end')
                                <p class="text-red-400 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <button type="submit" class="px-6 py-3 bg-gradient-to-r from-cyan-500 to-blue-600 text-white font-semibold rounded-xl hover:from-cyan-400 hover:to-blue-500 transition-all shadow-lg shadow-cyan-500/25"
                                {{ $availableComputers->isEmpty() ? 'disabled' : '' }}>
                            Buat Booking
                        </button>
                        <a href="{{ route('bookings.index') }}" class="px-6 py-3 bg-gray-800 text-gray-300 font-semibold rounded-xl hover:bg-gray-700 hover:text-white transition-all border border-gray-700">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
