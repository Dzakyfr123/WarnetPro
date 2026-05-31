<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-white">Network Scanner</h2>
                <p class="text-sm text-gray-400 mt-1">Deteksi & daftarkan PC client di jaringan</p>
            </div>
            <a href="{{ route('computers.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-800 text-gray-300 text-sm font-semibold rounded-xl hover:bg-gray-700 hover:text-white transition-all duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Server Info Card --}}
            <div class="bg-gray-900/50 backdrop-blur-sm border border-violet-500/20 rounded-2xl p-6 mb-6">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-12 h-12 rounded-xl bg-violet-500/10 flex items-center justify-center">
                        <svg class="w-6 h-6 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-white">Server Info</h3>
                        <p class="text-sm text-gray-400">Informasi server WarnetPro</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="bg-gray-800/50 rounded-xl px-4 py-3">
                        <div class="text-xs text-gray-500 uppercase tracking-wider mb-1">Server IP</div>
                        <div class="text-lg font-mono font-bold text-violet-400" id="serverIp">{{ $serverIp }}</div>
                    </div>
                    <div class="bg-gray-800/50 rounded-xl px-4 py-3">
                        <div class="text-xs text-gray-500 uppercase tracking-wider mb-1">Port</div>
                        <div class="text-lg font-mono font-bold text-cyan-400">{{ request()->getPort() }}</div>
                    </div>
                    <div class="bg-gray-800/50 rounded-xl px-4 py-3">
                        <div class="text-xs text-gray-500 uppercase tracking-wider mb-1">PC Terdaftar</div>
                        <div class="text-lg font-mono font-bold text-emerald-400">{{ $registeredCount }}</div>
                    </div>
                </div>
            </div>

            {{-- How To Card --}}
            <div class="bg-gray-900/50 backdrop-blur-sm border border-gray-700/50 rounded-2xl p-6 mb-6">
                <h4 class="text-sm font-bold text-gray-300 mb-3 flex items-center gap-2">
                    <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Cara Menggunakan
                </h4>
                <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 text-sm">
                    <div class="flex items-start gap-3 bg-gray-800/30 rounded-xl p-3">
                        <span class="flex-shrink-0 w-7 h-7 rounded-lg bg-violet-500/20 text-violet-400 flex items-center justify-center text-xs font-bold">1</span>
                        <span class="text-gray-400">Jalankan <code class="text-violet-300 bg-violet-500/10 px-1.5 py-0.5 rounded">warnetpro_client.py</code> di setiap PC</span>
                    </div>
                    <div class="flex items-start gap-3 bg-gray-800/30 rounded-xl p-3">
                        <span class="flex-shrink-0 w-7 h-7 rounded-lg bg-violet-500/20 text-violet-400 flex items-center justify-center text-xs font-bold">2</span>
                        <span class="text-gray-400">Klik <strong class="text-white">Scan Network</strong> di bawah</span>
                    </div>
                    <div class="flex items-start gap-3 bg-gray-800/30 rounded-xl p-3">
                        <span class="flex-shrink-0 w-7 h-7 rounded-lg bg-violet-500/20 text-violet-400 flex items-center justify-center text-xs font-bold">3</span>
                        <span class="text-gray-400">PC yang aktif akan muncul otomatis</span>
                    </div>
                    <div class="flex items-start gap-3 bg-gray-800/30 rounded-xl p-3">
                        <span class="flex-shrink-0 w-7 h-7 rounded-lg bg-violet-500/20 text-violet-400 flex items-center justify-center text-xs font-bold">4</span>
                        <span class="text-gray-400">Klik <strong class="text-emerald-400">Register</strong> untuk mendaftarkan</span>
                    </div>
                </div>
            </div>

            {{-- Scan Button --}}
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <h3 class="text-lg font-bold text-white">PC Terdeteksi</h3>
                    <span id="discoveredCount" class="px-2.5 py-1 text-xs font-bold rounded-full bg-violet-500/10 text-violet-400 border border-violet-500/20">
                        {{ count($discoveredPcs) }}
                    </span>
                </div>
                <button id="btnScan" onclick="scanNetwork()"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-violet-500 to-purple-600 text-white text-sm font-semibold rounded-xl hover:from-violet-400 hover:to-purple-500 transition-all duration-200 shadow-lg shadow-violet-500/25 disabled:opacity-50 disabled:cursor-not-allowed">
                    <svg id="scanIcon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <svg id="scanSpinner" class="w-4 h-4 animate-spin hidden" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                    <span id="scanText">Scan Network</span>
                </button>
            </div>

            {{-- Results Table --}}
            <div class="bg-gray-900/50 backdrop-blur-sm border border-gray-700/50 rounded-2xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-800">
                                <th class="text-left px-6 py-4 text-xs font-semibold uppercase tracking-wider text-gray-500">PC Name</th>
                                <th class="text-left px-6 py-4 text-xs font-semibold uppercase tracking-wider text-gray-500">IP Address</th>
                                <th class="text-left px-6 py-4 text-xs font-semibold uppercase tracking-wider text-gray-500">MAC Address</th>
                                <th class="text-left px-6 py-4 text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                                <th class="text-left px-6 py-4 text-xs font-semibold uppercase tracking-wider text-gray-500">Terakhir Online</th>
                                <th class="text-right px-6 py-4 text-xs font-semibold uppercase tracking-wider text-gray-500">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="pcTableBody">
                            @forelse($discoveredPcs as $pc)
                            <tr class="border-b border-gray-800/50 hover:bg-gray-800/30 transition-colors" id="row-{{ $pc->id }}">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-lg bg-violet-500/10 flex items-center justify-center">
                                            <svg class="w-4 h-4 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                        </div>
                                        <span class="font-semibold text-white">{{ $pc->pc_name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 font-mono text-cyan-400">{{ $pc->ip_address ?? '—' }}</td>
                                <td class="px-6 py-4 font-mono text-gray-500 text-xs">{{ $pc->mac_address ?? '—' }}</td>
                                <td class="px-6 py-4">
                                    @if($pc->isOnline())
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span> Online
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-500/10 text-gray-500 border border-gray-600/20">
                                            <span class="w-1.5 h-1.5 rounded-full bg-gray-500"></span> Offline
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-gray-500 text-xs">{{ $pc->last_heartbeat?->diffForHumans() ?? '—' }}</td>
                                <td class="px-6 py-4 text-right">
                                    <button onclick="registerPc({{ $pc->id }}, '{{ $pc->pc_name }}')"
                                        id="btn-{{ $pc->id }}"
                                        class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-500/10 text-emerald-400 text-xs font-semibold rounded-lg hover:bg-emerald-500/20 border border-emerald-500/20 transition-all">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        Register
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr id="emptyRow">
                                <td colspan="6" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center gap-4">
                                        <div class="w-16 h-16 rounded-2xl bg-gray-800/50 flex items-center justify-center">
                                            <svg class="w-8 h-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                        </div>
                                        <div>
                                            <p class="text-gray-500 font-medium">Tidak ada PC terdeteksi</p>
                                            <p class="text-gray-600 text-xs mt-1">Pastikan client berjalan di PC dan klik Scan Network</p>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Register All Button --}}
            @if(count($discoveredPcs) > 0)
            <div class="flex justify-end mt-4">
                <button onclick="registerAll()"
                    id="btnRegisterAll"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-600 text-white text-sm font-semibold rounded-xl hover:from-emerald-400 hover:to-teal-500 transition-all duration-200 shadow-lg shadow-emerald-500/25">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Register Semua ({{ count($discoveredPcs) }})
                </button>
            </div>
            @endif

        </div>
    </div>

    @push('scripts')
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        // ── Scan Network ─────────────────────────────────────────────────
        function scanNetwork() {
            const btn     = document.getElementById('btnScan');
            const icon    = document.getElementById('scanIcon');
            const spinner = document.getElementById('scanSpinner');
            const text    = document.getElementById('scanText');

            btn.disabled = true;
            icon.classList.add('hidden');
            spinner.classList.remove('hidden');
            text.textContent = 'Scanning...';

            fetch('{{ route("computers.scan") }}', {
                headers: { 'Accept': 'application/json' }
            })
            .then(r => r.json())
            .then(data => {
                if (data.status === 'ok') {
                    document.getElementById('discoveredCount').textContent = data.count;

                    if (data.discovered.length > 0) {
                        const tbody = document.getElementById('pcTableBody');
                        // Remove empty row if exists
                        const emptyRow = document.getElementById('emptyRow');
                        if (emptyRow) emptyRow.remove();

                        data.discovered.forEach(pc => {
                            // Check if row already exists
                            if (document.getElementById('row-' + pc.id)) return;

                            const tr = document.createElement('tr');
                            tr.className = 'border-b border-gray-800/50 hover:bg-gray-800/30 transition-colors';
                            tr.id = 'row-' + pc.id;
                            tr.style.animation = 'fadeIn 0.4s ease-out';
                            tr.innerHTML = `
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-lg bg-violet-500/10 flex items-center justify-center">
                                            <svg class="w-4 h-4 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                        </div>
                                        <span class="font-semibold text-white">${pc.pc_name}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 font-mono text-cyan-400">${pc.ip_address || '—'}</td>
                                <td class="px-6 py-4 font-mono text-gray-500 text-xs">${pc.mac_address || '—'}</td>
                                <td class="px-6 py-4">
                                    ${pc.is_online
                                        ? '<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20"><span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span> Online</span>'
                                        : '<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-500/10 text-gray-500 border border-gray-600/20"><span class="w-1.5 h-1.5 rounded-full bg-gray-500"></span> Offline</span>'
                                    }
                                </td>
                                <td class="px-6 py-4 text-gray-500 text-xs">${pc.last_heartbeat || '—'}</td>
                                <td class="px-6 py-4 text-right">
                                    <button onclick="registerPc(${pc.id}, '${pc.pc_name}')" id="btn-${pc.id}"
                                        class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-500/10 text-emerald-400 text-xs font-semibold rounded-lg hover:bg-emerald-500/20 border border-emerald-500/20 transition-all">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        Register
                                    </button>
                                </td>
                            `;
                            tbody.appendChild(tr);
                        });
                    }
                }
            })
            .catch(err => {
                console.error(err);
            })
            .finally(() => {
                btn.disabled = false;
                icon.classList.remove('hidden');
                spinner.classList.add('hidden');
                text.textContent = 'Scan Network';
            });
        }

        // ── Register Single PC ───────────────────────────────────────────
        function registerPc(id, pcName) {
            const btn = document.getElementById('btn-' + id);
            if (!btn) return;

            btn.disabled = true;
            btn.innerHTML = `
                <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                Registering...
            `;

            fetch('{{ route("computers.register") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ computer_id: id, pc_name: pcName }),
            })
            .then(r => r.json())
            .then(data => {
                if (data.status === 'ok') {
                    // Success animation
                    const row = document.getElementById('row-' + id);
                    if (row) {
                        btn.className = 'inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-500/20 text-emerald-300 text-xs font-semibold rounded-lg border border-emerald-500/30';
                        btn.innerHTML = `
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            ✓ Terdaftar
                        `;
                        btn.disabled = true;

                        // Fade out row after delay
                        setTimeout(() => {
                            row.style.transition = 'opacity 0.5s, transform 0.5s';
                            row.style.opacity = '0';
                            row.style.transform = 'translateX(20px)';
                            setTimeout(() => row.remove(), 500);

                            // Update counter
                            const counter = document.getElementById('discoveredCount');
                            const current = parseInt(counter.textContent) || 0;
                            counter.textContent = Math.max(0, current - 1);
                        }, 1500);
                    }
                } else {
                    alert(data.message || 'Gagal mendaftarkan PC.');
                    btn.disabled = false;
                    btn.innerHTML = `
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Register
                    `;
                }
            })
            .catch(err => {
                console.error(err);
                btn.disabled = false;
                btn.innerHTML = `Register`;
            });
        }

        // ── Register All ─────────────────────────────────────────────────
        function registerAll() {
            const rows = document.querySelectorAll('[id^="row-"]');
            rows.forEach((row, idx) => {
                const id = row.id.replace('row-', '');
                const nameEl = row.querySelector('.font-semibold.text-white');
                const name = nameEl ? nameEl.textContent.trim() : 'PC';
                setTimeout(() => registerPc(parseInt(id), name), idx * 300);
            });
        }

        // ── Auto-scan on page load ───────────────────────────────────────
        document.addEventListener('DOMContentLoaded', () => {
            // Auto refresh every 5 seconds
            setInterval(scanNetwork, 5000);
        });
    </script>
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to   { opacity: 1; transform: translateY(0); }
        }
    </style>
    @endpush
</x-app-layout>
