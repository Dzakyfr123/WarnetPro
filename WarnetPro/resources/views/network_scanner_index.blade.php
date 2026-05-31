<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            🔍 Network Scanner & PC Discovery
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Server Info Card -->
            <div class="bg-gradient-to-r from-blue-50 to-blue-100 border-2 border-blue-300 rounded-lg p-6 shadow-md">
                <h3 class="text-lg font-semibold text-blue-900 mb-4">📡 Server Information</h3>
                <div id="serverInfo" class="space-y-3">
                    <div class="flex items-center justify-center">
                        <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600 mr-3"></div>
                        <span class="text-blue-700">Loading server info...</span>
                    </div>
                </div>
                <div class="mt-4 p-3 bg-blue-100 rounded border border-blue-300 text-sm text-blue-800">
                    💡 <strong>Tip:</strong> Gunakan IP ini di config.ini setiap PC client
                </div>
            </div>

            <!-- Scan Button Card -->
            <div class="bg-white rounded-lg shadow-md p-6 border-t-4 border-blue-600">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">🔎 Network Scan</h3>
                <button 
                    id="scanBtn" 
                    onclick="scanNetwork()"
                    class="px-8 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg 
                           hover:from-blue-700 hover:to-blue-800 transition duration-200 font-semibold 
                           shadow-md hover:shadow-lg"
                >
                    🔍 Scan Network for PCs
                </button>
                
                <div id="scanStatus" class="mt-4 hidden p-4 bg-blue-50 rounded-lg border border-blue-200">
                    <div class="flex items-center space-x-3">
                        <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-blue-600"></div>
                        <span id="scanStatusText" class="text-blue-700 font-semibold">Scanning network...</span>
                    </div>
                    <p class="text-blue-600 text-sm mt-2">⏱️ Ini bisa memakan waktu 1-2 menit, mohon tunggu...</p>
                </div>

                <div id="scanError" class="mt-4 hidden p-4 bg-red-50 rounded-lg border border-red-200">
                    <p class="text-red-700 font-semibold" id="scanErrorText"></p>
                </div>
            </div>

            <!-- Discovered PCs Table -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden border-t-4 border-green-600">
                <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-green-50 to-green-100">
                    <h3 class="text-lg font-semibold text-gray-800">✨ Discovered PCs</h3>
                    <p class="text-gray-600 text-sm mt-1" id="resultCount">Click "Scan Network" to start</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-100 border-b-2 border-gray-300">
                            <tr>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">IP Address</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Hostname</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">PC Name</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Action</th>
                            </tr>
                        </thead>
                        <tbody id="resultTable" class="divide-y divide-gray-200">
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                    🔍 Klik tombol "Scan Network" untuk menemukan PC di jaringan
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Registered PCs Card -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden border-t-4 border-purple-600">
                <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-purple-50 to-purple-100">
                    <h3 class="text-lg font-semibold text-gray-800">✅ Registered PCs</h3>
                    <p class="text-gray-600 text-sm mt-1">Total: <span class="font-semibold">{{ $computers->count() }}</span> PC(s)</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-100 border-b-2 border-gray-300">
                            <tr>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">PC Name</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">IP Address</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Last Seen</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($computers as $computer)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 font-semibold text-gray-900">{{ $computer->pc_name }}</td>
                                <td class="px-6 py-4 font-mono text-sm text-gray-600">{{ $computer->ip_address ?? '—' }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 rounded-full text-sm font-semibold inline-flex items-center gap-1
                                        @if($computer->isOnline()) bg-green-100 text-green-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        @if($computer->isOnline())
                                            🟢 Online
                                        @else
                                            🔴 Offline
                                        @endif
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    @if($computer->last_heartbeat)
                                        {{ $computer->last_heartbeat->diffForHumans() }}
                                    @else
                                        Never
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                    Belum ada PC terdaftar. Gunakan Network Scanner untuk mendaftarkan PC.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Instructions Card -->
            <div class="bg-yellow-50 border-2 border-yellow-300 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-yellow-900 mb-4">📋 Cara Menggunakan</h3>
                <ol class="space-y-3 text-yellow-900">
                    <li class="flex gap-3">
                        <span class="font-bold bg-yellow-200 px-3 py-1 rounded-full">1</span>
                        <span>Klik tombol "Scan Network" untuk mencari PC yang terkoneksi</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="font-bold bg-yellow-200 px-3 py-1 rounded-full">2</span>
                        <span>Tunggu proses scanning selesai (1-2 menit)</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="font-bold bg-yellow-200 px-3 py-1 rounded-full">3</span>
                        <span>Untuk PC baru, klik tombol "Register" dan masukkan nama PC</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="font-bold bg-yellow-200 px-3 py-1 rounded-full">4</span>
                        <span>PC akan otomatis terdaftar dan muncul di daftar "Registered PCs"</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="font-bold bg-yellow-200 px-3 py-1 rounded-full">5</span>
                        <span>Bagikan Server IP ke setiap PC dan jalankan client mereka</span>
                    </li>
                </ol>
            </div>
        </div>
    </div>

    <script>
        let scannedPCs = [];
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        // Load server info saat page load
        async function loadServerInfo() {
            try {
                const response = await fetch('/api/server-ip');
                const data = await response.json();
                
                if (data.success) {
                    document.getElementById('serverInfo').innerHTML = `
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-white p-4 rounded-lg border border-blue-300">
                                <p class="text-blue-600 text-sm font-semibold">Server IP</p>
                                <p class="font-mono text-lg font-bold text-blue-900 mt-1">${data.ip}</p>
                                <button onclick="copyToClipboard('${data.ip}')" class="mt-2 text-blue-600 hover:text-blue-800 text-xs font-semibold">
                                    📋 Copy
                                </button>
                            </div>
                            <div class="bg-white p-4 rounded-lg border border-blue-300">
                                <p class="text-blue-600 text-sm font-semibold">Port</p>
                                <p class="font-mono text-lg font-bold text-blue-900 mt-1">${data.port}</p>
                            </div>
                            <div class="bg-white p-4 rounded-lg border border-blue-300">
                                <p class="text-blue-600 text-sm font-semibold">Full URL</p>
                                <p class="font-mono text-sm font-bold text-blue-900 mt-1">${data.url}</p>
                                <button onclick="copyToClipboard('${data.url}')" class="mt-2 text-blue-600 hover:text-blue-800 text-xs font-semibold">
                                    📋 Copy
                                </button>
                            </div>
                        </div>
                    `;
                }
            } catch (error) {
                console.error('Error loading server info:', error);
                document.getElementById('serverInfo').innerHTML = `
                    <p class="text-red-600">Error loading server info: ${error.message}</p>
                `;
            }
        }

        // Copy to clipboard
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                alert('✓ Copied: ' + text);
            });
        }

        // Scan network
        async function scanNetwork() {
            const btn = document.getElementById('scanBtn');
            const status = document.getElementById('scanStatus');
            const statusText = document.getElementById('scanStatusText');
            const error = document.getElementById('scanError');

            btn.disabled = true;
            status.classList.remove('hidden');
            error.classList.add('hidden');

            try {
                statusText.textContent = 'Scanning network... (ini bisa memakan waktu 1-2 menit)';
                
                const response = await fetch('/api/network-scan', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                    }
                });

                const data = await response.json();

                if (data.success) {
                    scannedPCs = data.data;
                    displayResults(data.data);
                    statusText.textContent = `✓ Ditemukan ${data.total} PC yang aktif`;
                    setTimeout(() => status.classList.add('hidden'), 2000);
                } else {
                    throw new Error(data.error || 'Unknown error');
                }
            } catch (err) {
                error.classList.remove('hidden');
                document.getElementById('scanErrorText').textContent = '❌ Error: ' + err.message;
            } finally {
                btn.disabled = false;
            }
        }

        // Display results
        function displayResults(results) {
            const table = document.getElementById('resultTable');
            const count = document.getElementById('resultCount');

            if (results.length === 0) {
                table.innerHTML = `
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                            ⚠️ Tidak ada PC ditemukan di network
                        </td>
                    </tr>
                `;
                count.textContent = 'No PCs found';
                return;
            }

            count.textContent = `✓ Ditemukan ${results.length} PC`;

            table.innerHTML = results.map(pc => `
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 font-mono text-sm font-semibold text-gray-900">${pc.ip}</td>
                    <td class="px-6 py-4 text-gray-700">${pc.hostname}</td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 rounded-full text-sm font-semibold inline-flex items-center gap-1
                            ${pc.status === 'registered' 
                                ? 'bg-green-100 text-green-800' 
                                : 'bg-yellow-100 text-yellow-800'}">
                            ${pc.status === 'registered' 
                                ? '✓ Registered' 
                                : '⚠️ New'}
                        </span>
                    </td>
                    <td class="px-6 py-4 font-semibold text-gray-900">${pc.pc_name || '—'}</td>
                    <td class="px-6 py-4">
                        ${pc.status === 'registered' 
                            ? `<span class="text-gray-500 text-sm">Sudah didaftar</span>`
                            : `<button 
                                onclick="showRegisterForm('${pc.ip}', '${pc.hostname}')"
                                class="px-4 py-2 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-lg 
                                       hover:from-green-700 hover:to-green-800 transition duration-200 text-sm font-semibold
                                       shadow-md">
                                ✅ Register
                            </button>`
                        }
                    </td>
                </tr>
            `).join('');
        }

        // Show register form
        function showRegisterForm(ip, hostname) {
            const defaultName = `PC-${ip.split('.')[3]}`;
            const pcName = prompt(`Masukkan nama PC untuk IP ${ip}:`, defaultName);
            
            if (pcName && pcName.trim()) {
                registerPC(ip, hostname, pcName.trim());
            }
        }

        // Register PC
        async function registerPC(ip, hostname, pcName) {
            try {
                const response = await fetch('/api/network-register', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        ip_address: ip,
                        hostname: hostname,
                        pc_name: pcName,
                    })
                });

                const data = await response.json();

                if (data.success) {
                    alert(`✓ PC "${pcName}" berhasil didaftarkan!\n\nRefresh page untuk melihat perubahan.`);
                    location.reload();
                } else {
                    alert(`❌ Error: ${data.error}`);
                }
            } catch (error) {
                alert(`❌ Error: ${error.message}`);
            }
        }

        // Load info on page load
        document.addEventListener('DOMContentLoaded', loadServerInfo);
    </script>
</x-app-layout>
