<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-white">Manajemen Komputer</h2>
                <p class="text-sm text-gray-400 mt-1">Kelola semua PC warnet</p>
            </div>
            @if(Auth::user()->isAdmin())
            <div class="flex items-center gap-3">
                <a href="{{ route('computers.scanner') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-violet-500 to-purple-600 text-white text-sm font-semibold rounded-xl hover:from-violet-400 hover:to-purple-500 transition-all duration-200 shadow-lg shadow-violet-500/25">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    Network Scanner
                </a>
                <a href="{{ route('computers.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-cyan-500 to-blue-600 text-white text-sm font-semibold rounded-xl hover:from-cyan-400 hover:to-blue-500 transition-all duration-200 shadow-lg shadow-cyan-500/25">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    Tambah PC
                </a>
            </div>
            @endif
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Filter Tabs -->
            <div class="flex items-center gap-2 mb-6 overflow-x-auto pb-2">
                <a href="{{ route('computers.index', ['status' => 'all']) }}"
                   class="px-4 py-2 rounded-xl text-sm font-medium transition-all whitespace-nowrap {{ $status === 'all' ? 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20' : 'bg-gray-800 text-gray-400 border border-gray-700 hover:text-white' }}">
                    Semua
                </a>
                <a href="{{ route('computers.index', ['status' => 'available']) }}"
                   class="px-4 py-2 rounded-xl text-sm font-medium transition-all whitespace-nowrap {{ $status === 'available' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-gray-800 text-gray-400 border border-gray-700 hover:text-white' }}">
                    🟢 Available
                </a>
                <a href="{{ route('computers.index', ['status' => 'used']) }}"
                   class="px-4 py-2 rounded-xl text-sm font-medium transition-all whitespace-nowrap {{ $status === 'used' ? 'bg-blue-500/10 text-blue-400 border border-blue-500/20' : 'bg-gray-800 text-gray-400 border border-gray-700 hover:text-white' }}">
                    🔵 Digunakan
                </a>
                <a href="{{ route('computers.index', ['status' => 'booking']) }}"
                   class="px-4 py-2 rounded-xl text-sm font-medium transition-all whitespace-nowrap {{ $status === 'booking' ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20' : 'bg-gray-800 text-gray-400 border border-gray-700 hover:text-white' }}">
                    🟡 Booking
                </a>
                <a href="{{ route('computers.index', ['status' => 'offline']) }}"
                   class="px-4 py-2 rounded-xl text-sm font-medium transition-all whitespace-nowrap {{ $status === 'offline' ? 'bg-gray-500/10 text-gray-400 border border-gray-500/20' : 'bg-gray-800 text-gray-400 border border-gray-700 hover:text-white' }}">
                    ⚫ Offline
                </a>
            </div>

            <!-- PC Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @forelse($computers as $computer)
                    <div class="bg-gray-900/50 backdrop-blur-sm border rounded-2xl p-5 transition-all duration-300 hover:shadow-lg group
                        @if($computer->status === 'available') border-emerald-500/20 hover:border-emerald-500/40 hover:shadow-emerald-500/5
                        @elseif($computer->status === 'used') border-blue-500/20 hover:border-blue-500/40 hover:shadow-blue-500/5
                        @elseif($computer->status === 'booking') border-amber-500/20 hover:border-amber-500/40 hover:shadow-amber-500/5
                        @else border-gray-700/50 hover:border-gray-600
                        @endif">

                        <!-- Header -->
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-xl flex items-center justify-center
                                    @if($computer->status === 'available') bg-emerald-500/10
                                    @elseif($computer->status === 'used') bg-blue-500/10
                                    @elseif($computer->status === 'booking') bg-amber-500/10
                                    @else bg-gray-700/50
                                    @endif">
                                    <svg class="w-6 h-6
                                        @if($computer->status === 'available') text-emerald-400
                                        @elseif($computer->status === 'used') text-blue-400
                                        @elseif($computer->status === 'booking') text-amber-400
                                        @else text-gray-500
                                        @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-white">{{ $computer->pc_name }}</h3>
                                    <span class="inline-flex items-center gap-1.5 text-xs font-medium
                                        @if($computer->status === 'available') text-emerald-400
                                        @elseif($computer->status === 'used') text-blue-400
                                        @elseif($computer->status === 'booking') text-amber-400
                                        @else text-gray-500
                                        @endif">
                                        <span class="w-1.5 h-1.5 rounded-full
                                            @if($computer->status === 'available') bg-emerald-400
                                            @elseif($computer->status === 'used') bg-blue-400
                                            @elseif($computer->status === 'booking') bg-amber-400
                                            @else bg-gray-600
                                            @endif"></span>
                                        {{ ucfirst($computer->status) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Session Info -->
                        @if($computer->activeSession)
                            <div class="bg-blue-500/5 border border-blue-500/10 rounded-xl p-3 mb-4">
                                <div class="text-xs text-gray-400">Customer</div>
                                <div class="text-sm font-semibold text-white">{{ $computer->activeSession->customer_name }}</div>
                                <div class="text-xs text-gray-400 mt-2">Sisa Waktu</div>
                                <div class="text-lg font-mono font-bold text-blue-400 session-timer" data-remaining="{{ $computer->activeSession->getRealRemainingSeconds() }}">--:--</div>
                            </div>
                        @elseif($computer->activeBooking)
                            <div class="bg-amber-500/5 border border-amber-500/10 rounded-xl p-3 mb-4">
                                <div class="text-xs text-gray-400">Booking oleh</div>
                                <div class="text-sm font-semibold text-white">{{ $computer->activeBooking->customer_name }}</div>
                                <div class="text-xs text-amber-400 mt-1">{{ $computer->activeBooking->booking_start->format('H:i') }} - {{ $computer->activeBooking->booking_end->format('H:i') }}</div>
                            </div>
                        @endif

                        <!-- Actions -->
                        @if(Auth::user()->isAdmin())
                        <div class="space-y-2 pt-3 border-t border-gray-800/50">
                            <!-- Row 1: Edit + Toggle -->
                            <div class="flex items-center gap-2">
                                <a href="{{ route('computers.edit', $computer) }}" class="flex-1 text-center px-3 py-2 bg-gray-800 text-gray-300 text-xs font-medium rounded-lg hover:bg-gray-700 hover:text-white transition-all">
                                    Edit
                                </a>
                                @if(in_array($computer->status, ['available', 'offline']))
                                    <form action="{{ route('computers.toggle', $computer) }}" method="POST" class="flex-1">
                                        @csrf
                                        <button type="submit" class="w-full px-3 py-2 text-xs font-medium rounded-lg transition-all
                                            {{ $computer->status === 'offline' ? 'bg-emerald-500/10 text-emerald-400 hover:bg-emerald-500/20' : 'bg-gray-600/10 text-gray-400 hover:bg-gray-600/20' }}">
                                            {{ $computer->status === 'offline' ? 'Nyalakan' : 'Matikan' }}
                                        </button>
                                    </form>
                                @endif
                                @if($computer->status === 'available')
                                    <form action="{{ route('computers.destroy', $computer) }}" method="POST" onsubmit="return confirm('Hapus PC ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="px-3 py-2 bg-red-500/10 text-red-400 text-xs font-medium rounded-lg hover:bg-red-500/20 transition-all">
                                            Hapus
                                        </button>
                                    </form>
                                @endif
                            </div>

                            <!-- Row 2: Operator Controls (Lock & Screenshot) -->
                            @if($computer->isOnline())
                            <div class="flex items-center gap-2">
                                <!-- Lock -->
                                <form action="{{ route('computers.lock', $computer) }}" method="POST" class="flex-1">
                                    @csrf
                                    <button type="submit"
                                        title="Kunci layar PC ini"
                                        class="w-full flex items-center justify-center gap-1 px-3 py-2 bg-orange-500/10 text-orange-400 text-xs font-semibold rounded-lg hover:bg-orange-500/20 transition-all">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/></svg>
                                        Kunci
                                    </button>
                                </form>
                                <!-- Unlock -->
                                <form action="{{ route('computers.unlock', $computer) }}" method="POST" class="flex-1">
                                    @csrf
                                    <button type="submit"
                                        title="Buka kunci PC ini"
                                        class="w-full flex items-center justify-center gap-1 px-3 py-2 bg-emerald-500/10 text-emerald-400 text-xs font-semibold rounded-lg hover:bg-emerald-500/20 transition-all">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a5 5 0 00-5 5v2a2 2 0 00-2 2v5a2 2 0 002 2h10a2 2 0 002-2v-5a2 2 0 00-2-2H7V7a3 3 0 015.905-.75 1 1 0 001.937-.5A5.002 5.002 0 0010 2z"/></svg>
                                        Buka
                                    </button>
                                </form>
                                <!-- Screenshot -->
                                <button
                                    type="button"
                                    onclick="openScreenshotModal({{ $computer->id }}, '{{ $computer->pc_name }}')"
                                    title="Lihat layar PC ini"
                                    class="flex-1 flex items-center justify-center gap-1 px-3 py-2 bg-violet-500/10 text-violet-400 text-xs font-semibold rounded-lg hover:bg-violet-500/20 transition-all cursor-pointer">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    Layar
                                </button>
                            </div>
                            @endif
                        </div>
                        @endif
                    </div>
                @empty
                    <div class="col-span-full text-center py-12">
                        <svg class="w-16 h-16 text-gray-700 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        <p class="text-gray-500">Tidak ada komputer ditemukan</p>
                    </div>
                @endforelse
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
                if (rem === 0) setTimeout(() => location.reload(), 2000);
            }, 1000);
        });

        // ── Screenshot Modal ───────────────────────────────────────────────
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        function openScreenshotModal(computerId, pcName) {
            const modal       = document.getElementById('screenshotModal');
            const imgEl       = document.getElementById('screenshotImg');
            const loadingEl   = document.getElementById('screenshotLoading');
            const loadingText = document.getElementById('screenshotLoadingText');
            const titleEl     = document.getElementById('screenshotTitle');
            const metaEl      = document.getElementById('screenshotMeta');
            const retryBtn    = document.getElementById('screenshotRetry');

            // Reset state
            titleEl.textContent = `Layar ${pcName}`;
            imgEl.classList.add('hidden');
            imgEl.src = '';
            loadingEl.classList.remove('hidden');
            loadingText.textContent = 'Mengirim permintaan ke client...';
            metaEl.textContent = '';
            retryBtn.onclick = () => openScreenshotModal(computerId, pcName);
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';

            const requestTime = Math.floor(Date.now() / 1000);

            // Step 1: Send screenshot request to server (which queues command for client)
            fetch(`/computers/${computerId}/screenshot`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                }
            })
            .then(r => r.json())
            .then(data => {
                if (data.status !== 'ok') {
                    loadingText.textContent = 'Gagal mengirim request. Coba lagi.';
                    return;
                }
                loadingText.textContent = 'Menunggu client mengambil screenshot...';

                // Step 2: Poll for the uploaded screenshot
                let attempts = 0;
                const maxAttempts = 20; // 20 × 2s = 40s max wait
                const pollInterval = setInterval(() => {
                    attempts++;
                    if (attempts > maxAttempts) {
                        clearInterval(pollInterval);
                        loadingText.textContent = '⚠ Timeout: Client tidak merespon. Pastikan client online.';
                        return;
                    }

                    loadingText.textContent = `Menunggu screenshot... (${attempts}/${maxAttempts})`;

                    fetch(`/api/client/screenshot/${pcName}`, { headers: { 'Accept': 'application/json' } })
                        .then(r => r.json())
                        .then(d => {
                            if (d.status === 'ok' && d.timestamp >= requestTime) {
                                clearInterval(pollInterval);
                                // Show image
                                const url = d.url + '?t=' + d.timestamp;
                                imgEl.onload = () => {
                                    loadingEl.classList.add('hidden');
                                    imgEl.classList.remove('hidden');
                                    const taken = new Date(d.timestamp * 1000);
                                    metaEl.textContent = `${pcName} · ${taken.toLocaleTimeString('id-ID')}`;
                                };
                                imgEl.onerror = () => {
                                    loadingText.textContent = 'Gagal memuat gambar. Coba lagi.';
                                };
                                imgEl.src = url;
                            }
                        })
                        .catch(() => {});
                }, 2000);
            })
            .catch(() => {
                loadingText.textContent = 'Koneksi ke server gagal.';
            });
        }

        function closeScreenshotModal() {
            document.getElementById('screenshotModal').classList.add('hidden');
            document.body.style.overflow = '';
            const imgEl = document.getElementById('screenshotImg');
            imgEl.src = ''; // Stop loading
        }

        // Close modal on backdrop click
        document.getElementById('screenshotModal')?.addEventListener('click', function(e) {
            if (e.target === this) closeScreenshotModal();
        });

        // Close modal on Escape
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') closeScreenshotModal();
        });
    </script>

    <!-- Screenshot Modal -->
    <div id="screenshotModal"
         class="hidden fixed inset-0 z-50 flex items-center justify-center p-4"
         style="background: rgba(0,0,0,0.85); backdrop-filter: blur(6px);">

        <div class="relative bg-gray-900 border border-gray-700/60 rounded-2xl shadow-2xl w-full max-w-4xl overflow-hidden"
             style="max-height: 90vh;">

            <!-- Modal Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-800">
                <div class="flex items-center gap-3">
                    <!-- Camera icon -->
                    <div class="w-9 h-9 rounded-xl bg-violet-500/10 flex items-center justify-center">
                        <svg class="w-5 h-5 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 id="screenshotTitle" class="text-white font-semibold text-base">Layar PC</h3>
                        <p id="screenshotMeta" class="text-gray-500 text-xs"></p>
                    </div>
                </div>
                <button onclick="closeScreenshotModal()"
                        class="w-8 h-8 rounded-lg bg-gray-800 text-gray-400 hover:text-white hover:bg-gray-700 transition-all flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-6" style="max-height: calc(90vh - 80px); overflow-y: auto;">

                <!-- Loading State -->
                <div id="screenshotLoading" class="flex flex-col items-center justify-center py-16 gap-6">
                    <!-- Spinner -->
                    <div class="relative">
                        <div class="w-16 h-16 border-4 border-gray-700 border-t-violet-500 rounded-full animate-spin"></div>
                        <div class="absolute inset-2 border-4 border-gray-800 border-b-violet-300/40 rounded-full animate-spin" style="animation-direction:reverse; animation-duration:1.5s"></div>
                    </div>
                    <p id="screenshotLoadingText"
                       class="text-gray-400 text-sm font-medium text-center">Memproses...</p>
                    <button id="screenshotRetry"
                            class="px-5 py-2 text-xs font-semibold text-violet-400 border border-violet-500/30 rounded-lg hover:bg-violet-500/10 transition-all">
                        Coba Lagi
                    </button>
                </div>

                <!-- Screenshot Image -->
                <img id="screenshotImg"
                     class="hidden w-full rounded-xl border border-gray-700/50 shadow-xl"
                     alt="Screenshot layar client"
                     style="image-rendering: auto;"/>
            </div>
        </div>
    </div>
    @endpush
</x-app-layout>
