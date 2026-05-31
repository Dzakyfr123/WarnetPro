# WarnetPro Client v2.0

Aplikasi client Python yang berjalan di setiap PC warnet. Client ini berkomunikasi dengan server WarnetPro (Laravel) untuk mengirim heartbeat, menerima perintah operator, mengelola sesi member, dan mendukung fitur remote control.

---

## 📋 Daftar Fitur

| # | Fitur | Deskripsi | Kontrol |
|---|-------|-----------|---------|
| 1 | **Heartbeat** | Kirim status online ke server setiap 5 detik | Otomatis |
| 2 | **Auto-Discovery** | PC baru otomatis terdeteksi di Network Scanner operator | Otomatis |
| 3 | **Status Polling** | Ambil info sesi aktif (timer countdown) setiap 3 detik | Otomatis |
| 4 | **Command Polling** | Terima & jalankan perintah dari operator setiap 2-3 detik | Otomatis |
| 5 | **Member Login** | Login member langsung dari PC client (username + password) | User |
| 6 | **Member Logout** | Logout member (sisa waktu disimpan ke akun) | User |
| 7 | **🔒 Lock Screen** | Layar kunci fullscreen — memblokir akses PC | Operator |
| 8 | **🔓 Unlock** | Buka kunci layar PC | Operator |
| 9 | **📷 Screenshot** | Capture layar & kirim ke operator dashboard | Operator |
| 10 | **💬 Message** | Tampilkan popup pesan dari operator | Operator |
| 11 | **⏻ Shutdown** | Matikan PC dalam 5 detik | Operator |
| 12 | **⟳ Restart** | Restart PC dalam 5 detik | Operator |
| 13 | **Sound Alerts** | Beep saat waktu hampir habis / habis | Otomatis |

---

## 🏗️ Arsitektur Sistem

```
┌─────────────────────────────────────────────────────────┐
│                   SERVER (Laravel)                       │
│                   php artisan serve                      │
│                                                         │
│  ┌─────────────┐  ┌──────────────┐  ┌───────────────┐  │
│  │  Dashboard   │  │  API Client  │  │  Screenshot   │  │
│  │  (Operator)  │  │  Endpoints   │  │  Storage      │  │
│  └──────┬───────┘  └──────┬───────┘  └───────────────┘  │
│         │                 │                              │
└─────────┼─────────────────┼──────────────────────────────┘
          │ HTTPS/HTTP      │ REST API
          │                 │
    ┌─────┴─────┐     ┌─────┴──────────────┐
    │ Browser   │     │ warnetpro_client.py │
    │ (Admin)   │     │ (setiap PC warnet)  │
    └───────────┘     └────────────────────┘
```

### Alur Komunikasi

```
CLIENT → SERVER (setiap 5 detik):
  POST /api/client/heartbeat
  Body: { pc_name, ip_address, mac_address }

CLIENT → SERVER (setiap 3 detik):
  GET /api/client/status/{pcName}
  Response: { session: { customer_name, remaining_seconds, ... } }

CLIENT → SERVER (setiap 2-3 detik):
  GET /api/client/commands/{pcName}
  Response: { commands: [{ id, type, payload }] }
  → Lalu: POST /api/client/commands/{id}/ack

SCREENSHOT FLOW:
  OPERATOR klik [📷 Layar]
    → POST /computers/{id}/screenshot         (buat PcCommand)
    → Client poll, terima "screenshot_request"
    → Client capture screen (Pillow)
    → POST /api/client/screenshot/upload       (upload JPEG)
    → OPERATOR poll GET /api/client/screenshot/{pcName}
    → Tampilkan gambar di modal dashboard

LOCK FLOW:
  OPERATOR klik [🔒 Kunci]
    → POST /computers/{id}/lock               (buat PcCommand)
    → Client poll, terima "lock"
    → Client tampilkan Tkinter fullscreen overlay
  OPERATOR klik [🔓 Buka]
    → POST /computers/{id}/unlock             (buat PcCommand)
    → Client poll, terima "unlock"
    → Client tutup lock screen
```

---

## 💻 Persyaratan Sistem

### PC Client
- **OS**: Windows 7/8/10/11 (recommended), Linux, macOS
- **Python**: 3.7 atau lebih baru
- **Library Python**:
  - `requests` — HTTP client untuk komunikasi dengan server
  - `Pillow` — Screenshot capture (untuk fitur Lihat Layar)
  - `tkinter` — Lock screen GUI (sudah termasuk di Python Windows)

### Server
- **PHP**: 8.1+
- **Laravel**: 12.x
- **Database**: MySQL / SQLite
- **Composer**: untuk install dependencies

---

## 🚀 Instalasi & Setup

### Langkah 1: Setup Server (WarnetPro)

```bash
# Clone repository
git clone <repo-url> WarnetPro
cd WarnetPro

# Install dependencies
composer install
npm install && npm run build

# Setup environment
cp .env.example .env
php artisan key:generate

# Setup database (edit .env dulu untuk DB credentials)
php artisan migrate --seed

# Buat symlink untuk screenshot storage
php artisan storage:link

# Jalankan server
php artisan serve --host=0.0.0.0 --port=8000
```

> ⚠️ Gunakan `--host=0.0.0.0` agar server bisa diakses dari PC lain di jaringan!

### Langkah 2: Setup Client (di setiap PC Warnet)

```bash
# Copy folder client ke setiap PC
# Atau akses dari network share

# Install dependencies
cd client
pip install -r requirements.txt
```

### Langkah 3: Konfigurasi Client

Edit file `config.ini`:

```ini
[server]
; Ganti dengan IP server sebenarnya (bukan localhost!)
url = http://192.168.1.100:8000
; Nama PC — harus unik di setiap PC
pc_name = PC-01

[client]
heartbeat_interval = 5       ; Detik antara heartbeat
status_poll_interval = 3     ; Detik antara polling status
command_poll_interval = 3    ; Detik antara polling perintah
screenshot_quality = 60      ; Kualitas JPEG (1-95)
```

> **Penting**: `pc_name` bisa bebas saat pertama kali. Nanti admin akan mendaftarkan PC dari **Network Scanner** di dashboard.

### Langkah 4: Jalankan Client

```bash
python warnetpro_client.py
```

Output yang muncul:
```
============================================================
  WarnetPro Client v2.0
  PC Name  : PC-01
  Server   : http://192.168.1.100:8000
  IP       : 192.168.1.50
  MAC      : aa:bb:cc:dd:ee:ff
------------------------------------------------------------
  Lock Screen : ✓ Tersedia
  Screenshot  : ✓ Tersedia
============================================================

[INFO] Testing connection to server...
[OK] Terhubung ke server!

[INFO] Client berjalan. Ketik "help" untuk melihat perintah.

warnetpro>
```

### Langkah 5: Register PC via Network Scanner

1. Buka browser → `http://192.168.1.100:8000`
2. Login sebagai admin
3. Klik menu **Komputer** → klik **Network Scanner**
4. Server IP ditampilkan otomatis
5. Klik **Scan Network**
6. Semua PC yang client-nya sudah berjalan akan muncul
7. Klik **Register** untuk setiap PC
8. Selesai! PC sudah terdaftar dan siap digunakan

---

## 🎮 Cara Penggunaan

### Dari Sisi Client (User di PC Warnet)

| Perintah | Deskripsi |
|----------|-----------|
| `login` | Login sebagai member (masukkan username + password) |
| `logout` | Logout member (sisa waktu disimpan ke akun) |
| `status` | Lihat status sesi saat ini (timer, nama, dll) |
| `info` | Lihat info client (IP, MAC, OS, fitur) |
| `help` | Tampilkan daftar perintah |
| `quit` | Keluar dari client (PC ditandai offline) |

### Dari Sisi Operator (Dashboard Web)

Di halaman **Manajemen Komputer**, setiap card PC yang online memiliki tombol:

| Tombol | Warna | Fungsi |
|--------|-------|--------|
| **Edit** | Abu-abu | Edit nama PC, status |
| **Kunci** | 🟠 Orange | Kunci layar PC (lock screen fullscreen) |
| **Buka** | 🟢 Hijau | Buka kunci layar PC |
| **Layar** | 🟣 Violet | Lihat screenshot layar PC (modal popup) |
| **Shutdown** | — | Kirim perintah matikan PC |
| **Restart** | — | Kirim perintah restart PC |

---

## 🔒 Fitur Lock Screen — Detail

Saat operator mengkunci PC:

1. Client menerima command `lock` dari server
2. Tkinter membuat **window fullscreen** di atas semua aplikasi
3. Window menampilkan:
   - Icon gembok besar 🔒
   - Teks "PC DIKUNCI OLEH OPERATOR"
   - Nama PC
   - Jam digital (bergerak)
   - Pesan "Hubungi operator untuk membuka kunci"
4. **Shortcut diblokir**: Alt-F4, Escape, Alt-Tab, Win key
5. Untuk membuka: operator klik **🔓 Buka** di dashboard

> Lock screen berjalan di thread terpisah sehingga tidak memblokir heartbeat dan polling.

---

## 📷 Fitur Screenshot — Detail

Saat operator melihat layar:

1. Operator klik **📷 Layar** di dashboard
2. Server kirim command `screenshot_request` ke client
3. Client menangkap layar menggunakan `Pillow.ImageGrab`
4. Screenshot di-resize (max 1280px) dan dikompresi JPEG
5. Upload ke server via `POST /api/client/screenshot/upload`
6. Dashboard polling dan menampilkan gambar di modal popup
7. Gambar disimpan di `storage/app/public/screenshots/{pc_name}_latest.jpg`

---

## 📡 API Endpoints

### Client API (tanpa auth, prefix `/api/client`)

| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| POST | `/heartbeat` | Kirim heartbeat (pc_name, ip, mac) |
| GET | `/status/{pcName}` | Ambil status sesi aktif |
| POST | `/member-login` | Login member dari client |
| POST | `/member-logout` | Logout member dari client |
| POST | `/offline` | Notifikasi PC offline |
| GET | `/commands/{pcName}` | Ambil perintah pending |
| POST | `/commands/{id}/ack` | Acknowledge perintah |
| POST | `/screenshot/upload` | Upload screenshot (multipart) |
| GET | `/screenshot/{pcName}` | Ambil URL screenshot terbaru |

### Operator Web Routes (auth required)

| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| POST | `/computers/{id}/lock` | Kunci layar PC |
| POST | `/computers/{id}/unlock` | Buka kunci PC |
| POST | `/computers/{id}/screenshot` | Request screenshot |
| POST | `/computers/{id}/shutdown` | Matikan PC |
| POST | `/computers/{id}/restart` | Restart PC |
| GET | `/computers/scanner` | Halaman Network Scanner |
| POST | `/computers/scanner/register` | Register PC baru |

---

## 🔧 Sound Alerts

Letakkan file suara di folder `sounds/`:

| File | Kapan Diputar |
|------|--------------|
| `notification.wav` | Menerima pesan dari operator |
| `login.wav` | Member berhasil login |
| `logout.wav` | Member berhasil logout |

> Jika file suara tidak ditemukan, client menggunakan system beep.

---

## ⚡ Auto-Start saat Windows Startup

### Cara 1: Folder Startup
1. Buat shortcut `warnetpro_client.py`
2. Tekan `Win + R`, ketik `shell:startup`, Enter
3. Pindahkan shortcut ke folder Startup

### Cara 2: Batch File
Buat file `start_client.bat`:
```batch
@echo off
cd /d "%~dp0"
python warnetpro_client.py
pause
```
Lalu taruh shortcut batch file di folder Startup.

---

## ❓ Troubleshooting

| Masalah | Solusi |
|---------|--------|
| `[WARN] Tidak dapat terhubung ke server` | Pastikan server berjalan dan IP di `config.ini` benar |
| `[ERROR] PC not found` | Register PC melalui **Network Scanner** di dashboard |
| Lock screen tidak muncul | Pastikan `tkinter` terinstal (`python -m tkinter`) |
| Screenshot tidak muncul di operator | Jalankan `php artisan storage:link` di server |
| Screenshot error | Install `Pillow`: `pip install Pillow` |
| Perintah shutdown tidak jalan | Jalankan client sebagai **Administrator** |
| `ModuleNotFoundError: requests` | Jalankan `pip install -r requirements.txt` |

---

## 📁 Struktur File

```
WarnetPro/
├── client/                          ← FOLDER CLIENT
│   ├── warnetpro_client.py          ← Aplikasi client utama
│   ├── config.ini                   ← Konfigurasi (server URL, pc_name)
│   ├── requirements.txt             ← Dependencies Python
│   ├── README.md                    ← File ini
│   └── sounds/                      ← Sound alerts (opsional)
│       ├── notification.wav
│       ├── login.wav
│       └── logout.wav
│
├── app/
│   ├── Http/Controllers/
│   │   ├── ComputerController.php   ← Lock, Unlock, Screenshot, Scanner
│   │   └── Api/
│   │       └── ClientApiController.php  ← Heartbeat, Status, Commands, Screenshot upload
│   └── Models/
│       ├── Computer.php             ← Model komputer (+ status 'unregistered')
│       └── PcCommand.php            ← Queue perintah ke client
│
├── routes/
│   ├── api.php                      ← API routes untuk client
│   └── web.php                      ← Web routes untuk operator
│
└── resources/views/
    └── computers/
        ├── index.blade.php          ← Halaman manajemen PC (+ tombol lock/screenshot)
        └── scanner.blade.php        ← Halaman Network Scanner
```
