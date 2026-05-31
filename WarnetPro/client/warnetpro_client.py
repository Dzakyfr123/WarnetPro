#!/usr/bin/env python3
"""
WarnetPro Client v2.0 — Aplikasi client yang berjalan di setiap PC warnet.
Mengirim heartbeat, menerima perintah (shutdown/restart/lock/unlock/screenshot),
dan menampilkan status sesi.

Fitur baru v2.0:
  - Lock Screen (Tkinter fullscreen overlay) — dikontrol operator
  - Screenshot capture & upload ke server — dikontrol operator
  - Unlock — operator membuka lock screen
"""

import configparser
import io
import json
import os
import platform
import socket
import subprocess
import sys
import threading
import time
import uuid
from datetime import datetime

import requests

# ── Optional dependencies ──────────────────────────────────────────────────
try:
    import tkinter as tk
    TK_AVAILABLE = True
except ImportError:
    TK_AVAILABLE = False

try:
    from PIL import Image, ImageGrab  # type: ignore
    PIL_AVAILABLE = True
except ImportError:
    PIL_AVAILABLE = False

# ============================================================================
# Configuration
# ============================================================================

CONFIG_FILE = os.path.join(os.path.dirname(os.path.abspath(__file__)), 'config.ini')
SOUNDS_DIR = os.path.join(os.path.dirname(os.path.abspath(__file__)), 'sounds')


def load_config():
    """Load configuration from config.ini."""
    config = configparser.ConfigParser()
    config.read(CONFIG_FILE)

    return {
        'server_url': config.get('server', 'url', fallback='http://127.0.0.1:8000'),
        'pc_name': config.get('server', 'pc_name', fallback=socket.gethostname()),
        'heartbeat_interval': config.getint('client', 'heartbeat_interval', fallback=5),
        'status_poll_interval': config.getint('client', 'status_poll_interval', fallback=3),
        'command_poll_interval': config.getint('client', 'command_poll_interval', fallback=3),
        'screenshot_quality': config.getint('client', 'screenshot_quality', fallback=60),
    }


# ============================================================================
# Network Utilities
# ============================================================================

def get_ip_address():
    """Get the local IP address of this machine."""
    try:
        s = socket.socket(socket.AF_INET, socket.SOCK_DGRAM)
        s.connect(('8.8.8.8', 80))
        ip = s.getsockname()[0]
        s.close()
        return ip
    except Exception:
        return '127.0.0.1'


def get_mac_address():
    """Get the MAC address of this machine."""
    try:
        mac = ':'.join(
            f'{(uuid.getnode() >> i) & 0xff:02x}'
            for i in range(0, 48, 8)
        )[::-1]
        # Fix reversal — build properly
        mac_int = uuid.getnode()
        mac = ':'.join(
            f'{(mac_int >> (8 * i)) & 0xff:02x}'
            for i in reversed(range(6))
        )
        return mac
    except Exception:
        return '00:00:00:00:00:00'


# ============================================================================
# Sound Alerts
# ============================================================================

def play_sound(sound_name):
    """Play a sound alert (cross-platform)."""
    sound_file = os.path.join(SOUNDS_DIR, sound_name)
    if not os.path.exists(sound_file):
        # Fallback: use system beep
        try:
            if platform.system() == 'Windows':
                import winsound
                winsound.Beep(1000, 500)
            else:
                print('\a', end='', flush=True)
        except Exception:
            pass
        return

    try:
        if platform.system() == 'Windows':
            import winsound
            winsound.PlaySound(sound_file, winsound.SND_FILENAME | winsound.SND_ASYNC)
        elif platform.system() == 'Darwin':
            subprocess.Popen(['afplay', sound_file])
        else:
            subprocess.Popen(['aplay', '-q', sound_file])
    except Exception:
        pass


def play_warning_beep():
    """Play a warning beep for low time alerts."""
    try:
        if platform.system() == 'Windows':
            import winsound
            winsound.Beep(800, 300)
            time.sleep(0.1)
            winsound.Beep(800, 300)
        else:
            print('\a', end='', flush=True)
    except Exception:
        pass


def play_time_up_beep():
    """Play an urgent beep when time is up."""
    try:
        if platform.system() == 'Windows':
            import winsound
            for freq in [600, 800, 1000]:
                winsound.Beep(freq, 200)
                time.sleep(0.05)
        else:
            print('\a\a\a', end='', flush=True)
    except Exception:
        pass


# ============================================================================
# Lock Screen (Tkinter Fullscreen Overlay)
# ============================================================================

class LockScreen:
    """
    Lock screen fullscreen overlay — dijalankan di thread terpisah.
    Saat operator mengirim command 'lock', layar ini menutup seluruh desktop
    sehingga user tidak bisa mengakses PC.
    """

    def __init__(self, pc_name):
        self.pc_name = pc_name
        self._thread = None
        self._root = None
        self._should_close = threading.Event()
        self._is_visible = threading.Event()

    def show(self):
        """Show the lock screen (starts Tkinter on its own thread)."""
        if not TK_AVAILABLE:
            print('[WARN] Tkinter tidak tersedia. Lock screen tidak bisa ditampilkan.')
            return
        if self._is_visible.is_set():
            return  # Already showing

        self._should_close.clear()
        self._thread = threading.Thread(target=self._run, daemon=True, name='lockscreen')
        self._thread.start()

    def hide(self):
        """Hide the lock screen."""
        if not self._is_visible.is_set():
            return
        self._should_close.set()
        # Trigger root to check the event
        if self._root:
            try:
                self._root.after(0, self._close_window)
            except Exception:
                pass

    def is_visible(self):
        return self._is_visible.is_set()

    def _run(self):
        """Tkinter main loop — runs in its own thread."""
        self._root = tk.Tk()
        root = self._root

        # ── Window setup ──────────────────────────────────────────────────
        root.attributes('-fullscreen', True)
        root.attributes('-topmost', True)
        root.configure(bg='#050814')
        root.overrideredirect(True)
        root.protocol("WM_DELETE_WINDOW", lambda: None)

        # Block common exit shortcuts
        for seq in ('<Alt-F4>', '<Escape>', '<Control-Escape>',
                    '<Super_L>', '<Super_R>', '<Alt-Tab>'):
            root.bind(seq, lambda e: 'break')

        scr_w = root.winfo_screenwidth()
        scr_h = root.winfo_screenheight()

        # ── Canvas background ─────────────────────────────────────────────
        canvas = tk.Canvas(root, width=scr_w, height=scr_h,
                           bg='#050814', highlightthickness=0)
        canvas.place(x=0, y=0)

        # Glowing circles behind icon
        cx, cy = scr_w // 2, scr_h // 2 - 60
        for r, col in [(140, '#0d1a3a'), (110, '#0f2050'), (80, '#112268')]:
            canvas.create_oval(cx - r, cy - r, cx + r, cy + r,
                               fill=col, outline='')

        # ── Center content ────────────────────────────────────────────────
        frame = tk.Frame(root, bg='#050814')
        frame.place(relx=0.5, rely=0.5, anchor='center')

        # Lock icon
        tk.Label(frame, text='🔒',
                 font=('Segoe UI Emoji', 72),
                 bg='#050814', fg='#ef4444').pack(pady=(0, 16))

        # Title
        tk.Label(frame,
                 text='PC TERKUNCI — HUBUNGI OPERATOR',
                 font=('Segoe UI', 22, 'bold'),
                 bg='#050814', fg='#ef4444').pack()

        # PC name
        tk.Label(frame,
                 text=self.pc_name,
                 font=('Segoe UI', 14),
                 bg='#050814', fg='#475569').pack(pady=(6, 30))

        # Separator
        tk.Frame(frame, width=420, height=1, bg='#1e293b').pack(pady=4)

        # Clock
        self._clock_lbl = tk.Label(frame,
                                    text='00:00:00',
                                    font=('Segoe UI', 40, 'bold'),
                                    bg='#050814', fg='#e2e8f0')
        self._clock_lbl.pack(pady=16)

        # Hint
        tk.Label(frame,
                 text='Hubungi operator untuk membuka kunci.',
                 font=('Segoe UI', 11),
                 bg='#050814', fg='#334155').pack(pady=(8, 0))

        # Corner watermark
        tk.Label(root, text='WarnetPro v2.0',
                 font=('Segoe UI', 8),
                 bg='#050814', fg='#1e293b').place(relx=1.0, rely=1.0,
                                                    anchor='se', x=-12, y=-8)

        root.focus_force()
        try:
            root.grab_set_global()
        except Exception:
            try:
                root.grab_set()
            except Exception:
                pass

        self._is_visible.set()

        # Start clock ticking
        self._tick_clock()

        # Enter mainloop
        root.mainloop()

        # After mainloop ends
        self._is_visible.clear()
        self._root = None

    def _tick_clock(self):
        """Update clock every second, also check for close event."""
        if self._should_close.is_set():
            self._close_window()
            return
        try:
            self._clock_lbl.config(text=datetime.now().strftime('%H:%M:%S'))
            self._root.after(1000, self._tick_clock)
        except Exception:
            pass

    def _close_window(self):
        """Destroy the lock screen window."""
        try:
            if self._root:
                self._root.grab_release()
                self._root.destroy()
        except Exception:
            pass


# ============================================================================
# Screenshot Capture & Upload
# ============================================================================

def capture_and_upload(base_url, pc_name, quality=60):
    """Capture the screen and upload to server."""
    if not PIL_AVAILABLE:
        print('[ERROR] Pillow tidak terinstal. Jalankan: pip install Pillow')
        return False

    try:
        print('[SCREENSHOT] Mengambil screenshot...')
        screenshot = ImageGrab.grab()

        # Scale down to max 1280px wide to reduce upload size
        w, h = screenshot.size
        if w > 1280:
            ratio = 1280 / w
            screenshot = screenshot.resize((1280, int(h * ratio)), Image.LANCZOS)

        buf = io.BytesIO()
        screenshot.save(buf, format='JPEG', quality=quality, optimize=True)
        buf.seek(0)

        url = f'{base_url}/api/client/screenshot/upload'
        files = {'screenshot': (f'{pc_name}.jpg', buf, 'image/jpeg')}
        data = {'pc_name': pc_name}
        resp = requests.post(url, files=files, data=data, timeout=15)

        if resp.status_code == 200:
            print('[SCREENSHOT] ✓ Screenshot berhasil dikirim ke server!')
            return True
        else:
            print(f'[SCREENSHOT] ✗ Upload gagal (HTTP {resp.status_code})')
            return False
    except Exception as e:
        print(f'[SCREENSHOT ERROR] {e}')
        return False


# ============================================================================
# API Client
# ============================================================================

class WarnetProClient:
    """Main client class for WarnetPro."""

    def __init__(self):
        self.config = load_config()
        self.base_url = self.config['server_url'].rstrip('/')
        self.pc_name = self.config['pc_name']
        self.ip_address = get_ip_address()
        self.mac_address = get_mac_address()

        self.running = False
        self.current_session = None
        self.last_remaining_seconds = None
        self.warning_played = False
        self.time_up_played = False

        self.is_locked = False

        # Lock screen instance
        self.lock_screen = LockScreen(self.pc_name)

        # Threads
        self._heartbeat_thread = None
        self._status_thread = None
        self._command_thread = None

    def api_url(self, path):
        """Build full API URL."""
        return f'{self.base_url}/api/client{path}'

    def api_get(self, path):
        """Make a GET request to the API."""
        try:
            response = requests.get(self.api_url(path), timeout=5)
            return response.json(), response.status_code
        except requests.exceptions.ConnectionError:
            return None, 0
        except Exception as e:
            print(f'[ERROR] GET {path}: {e}')
            return None, 0

    def api_post(self, path, data=None):
        """Make a POST request to the API."""
        try:
            response = requests.post(self.api_url(path), json=data or {}, timeout=5)
            return response.json(), response.status_code
        except requests.exceptions.ConnectionError:
            return None, 0
        except Exception as e:
            print(f'[ERROR] POST {path}: {e}')
            return None, 0

    # ------------------------------------------------------------------
    # Heartbeat
    # ------------------------------------------------------------------

    def send_heartbeat(self):
        """Send heartbeat to server."""
        data = {
            'pc_name': self.pc_name,
            'ip_address': self.ip_address,
            'mac_address': self.mac_address,
        }
        result, status = self.api_post('/heartbeat', data)
        if status == 200:
            return True
        elif status == 0:
            return False
        else:
            print(f'[WARN] Heartbeat failed: {result}')
            return False

    def heartbeat_loop(self):
        """Background loop for sending heartbeat."""
        interval = self.config['heartbeat_interval']
        while self.running:
            self.send_heartbeat()
            time.sleep(interval)

    # ------------------------------------------------------------------
    # Status Polling
    # ------------------------------------------------------------------

    def poll_status(self):
        """Poll current session status from server."""
        result, status = self.api_get(f'/status/{self.pc_name}')
        if status != 200 or result is None:
            return

        session = result.get('session')
        prev_session = self.current_session
        self.current_session = session

        if session:
            remaining = session.get('remaining_seconds', 0)
            customer = session.get('customer_name', 'Guest')

            # Display session info
            mins = remaining // 60
            secs = remaining % 60
            print(
                f'\r[{self.pc_name}] Sesi: {customer} '
                f'| Sisa: {mins:02d}:{secs:02d} '
                f'| Durasi: {session.get("duration_minutes", 0)} min   ',
                end='', flush=True
            )

            # Sound alerts
            if remaining <= 60 and not self.time_up_played and remaining > 0:
                print(f'\n[ALERT] Waktu hampir habis! ({remaining} detik tersisa)')
                play_warning_beep()
                self.warning_played = True

            if remaining <= 0 and not self.time_up_played:
                print(f'\n[ALERT] WAKTU HABIS!')
                play_time_up_beep()
                self.time_up_played = True

            self.last_remaining_seconds = remaining

            # Auto-unlock if session is active and operator did not lock manually
            if self.lock_screen.is_visible() and not self.is_locked:
                print(f'\n[INFO] Sesi aktif terdeteksi. Membuka kunci PC.')
                self.lock_screen.hide()
        else:
            # No active session
            if prev_session is not None:
                print(f'\n[{self.pc_name}] Sesi berakhir. PC dikunci.')
                self.warning_played = False
                self.time_up_played = False

            self.last_remaining_seconds = None

            # Auto-lock if no active session
            if not self.lock_screen.is_visible():
                print(f'\n[INFO] PC dikunci karena tidak ada sesi aktif.')
                self.lock_screen.show()

    def status_loop(self):
        """Background loop for polling status."""
        interval = self.config['status_poll_interval']
        while self.running:
            self.poll_status()
            time.sleep(interval)

    # ------------------------------------------------------------------
    # Command Polling
    # ------------------------------------------------------------------

    def poll_commands(self):
        """Poll and execute pending commands from server."""
        result, status = self.api_get(f'/commands/{self.pc_name}')
        if status != 200 or result is None:
            return

        commands = result.get('commands', [])
        for cmd in commands:
            cmd_id = cmd.get('id')
            cmd_type = cmd.get('type')
            payload = cmd.get('payload')

            print(f'\n[CMD] Received: {cmd_type}' + (f' — {payload}' if payload else ''))

            # Acknowledge the command
            self.api_post(f'/commands/{cmd_id}/ack')

            # Execute the command
            self.execute_command(cmd_type, payload)

    def execute_command(self, cmd_type, payload=None):
        """Execute a command received from the server."""
        if cmd_type == 'shutdown':
            print('[CMD] Shutting down PC...')
            self.notify_offline()
            if platform.system() == 'Windows':
                os.system('shutdown /s /t 5 /c "WarnetPro: PC dimatikan oleh operator"')
            else:
                os.system('sudo shutdown -h +0')

        elif cmd_type == 'restart':
            print('[CMD] Restarting PC...')
            self.notify_offline()
            if platform.system() == 'Windows':
                os.system('shutdown /r /t 5 /c "WarnetPro: PC di-restart oleh operator"')
            else:
                os.system('sudo reboot')

        elif cmd_type == 'message':
            print(f'[MSG] Pesan dari operator: {payload}')
            play_sound('notification.wav')
            # On Windows, show a message box
            if platform.system() == 'Windows':
                try:
                    import ctypes
                    ctypes.windll.user32.MessageBoxW(
                        0,
                        payload or 'Pesan dari operator',
                        'WarnetPro — Pesan',
                        0x40  # MB_ICONINFORMATION
                    )
                except Exception:
                    pass

        # ── NEW: Lock Screen ──────────────────────────────────────────────
        elif cmd_type == 'lock':
            print('[CMD] 🔒 PC dikunci oleh operator!')
            self.is_locked = True
            self.lock_screen.show()

        elif cmd_type == 'unlock':
            print('[CMD] 🔓 PC dibuka oleh operator!')
            self.is_locked = False
            self.lock_screen.hide()

        # ── NEW: Screenshot ───────────────────────────────────────────────
        elif cmd_type == 'screenshot_request':
            print('[CMD] 📷 Operator meminta screenshot layar...')
            # Run in separate thread so it doesn't block command polling
            threading.Thread(
                target=capture_and_upload,
                args=(self.base_url, self.pc_name,
                      self.config.get('screenshot_quality', 60)),
                daemon=True,
                name='screenshot'
            ).start()

    def command_loop(self):
        """Background loop for polling commands."""
        interval = self.config['command_poll_interval']
        while self.running:
            self.poll_commands()
            time.sleep(interval)



    # ------------------------------------------------------------------
    # Offline Notification
    # ------------------------------------------------------------------

    def notify_offline(self):
        """Notify server that this PC is going offline."""
        data = {'pc_name': self.pc_name}
        self.api_post('/offline', data)

    # ------------------------------------------------------------------
    # Main Loop
    # ------------------------------------------------------------------

    def start(self):
        """Start the client with all background threads."""
        self.running = True

        print('=' * 60)
        print(f'  WarnetPro Client v2.0')
        print(f'  PC Name  : {self.pc_name}')
        print(f'  Server   : {self.base_url}')
        print(f'  IP       : {self.ip_address}')
        print(f'  MAC      : {self.mac_address}')
        print('-' * 60)
        print(f'  Lock Screen : {"✓ Tersedia" if TK_AVAILABLE else "✗ Tkinter tidak ada"}')
        print(f'  Screenshot  : {"✓ Tersedia" if PIL_AVAILABLE else "✗ Install Pillow"}')
        print('=' * 60)
        print()

        # Test connection
        print('[INFO] Testing connection to server...')
        if self.send_heartbeat():
            print('[OK] Terhubung ke server!')
        else:
            print('[WARN] Tidak dapat terhubung ke server. Akan terus mencoba...')
        print()

        # Start background threads
        self._heartbeat_thread = threading.Thread(
            target=self.heartbeat_loop, daemon=True, name='heartbeat'
        )
        self._status_thread = threading.Thread(
            target=self.status_loop, daemon=True, name='status'
        )
        self._command_thread = threading.Thread(
            target=self.command_loop, daemon=True, name='commands'
        )

        self._heartbeat_thread.start()
        self._status_thread.start()
        self._command_thread.start()

        print('[INFO] Client berjalan. Ketik "help" untuk melihat perintah.')
        print()

        # Interactive command loop
        try:
            self.interactive_loop()
        except KeyboardInterrupt:
            print('\n\n[INFO] Shutting down client...')
        finally:
            self.stop()

    def stop(self):
        """Stop the client and notify server."""
        self.running = False
        # Close lock screen if active
        if self.lock_screen.is_visible():
            self.lock_screen.hide()
        print('[INFO] Mengirim notifikasi offline ke server...')
        self.notify_offline()
        print('[INFO] Client berhenti.')

    def interactive_loop(self):
        """Interactive command input loop."""
        while self.running:
            try:
                print()  # newline after status updates
                cmd = input('warnetpro> ').strip().lower()

                if not cmd:
                    continue
                elif cmd == 'help':
                    self.print_help()
                elif cmd == 'status':
                    self.print_status()
                elif cmd == 'info':
                    self.print_info()
                elif cmd in ('quit', 'exit', 'q'):
                    print('[INFO] Keluar...')
                    break
                else:
                    print(f'[ERROR] Perintah tidak dikenal: {cmd}. Ketik "help".')
            except EOFError:
                break

    def print_help(self):
        """Print available commands."""
        print()
        print('  Perintah yang tersedia:')
        print('  ─────────────────────────────────────')
        print('  status   — Tampilkan status sesi saat ini')
        print('  info     — Tampilkan info client')
        print('  help     — Tampilkan bantuan ini')
        print('  quit     — Keluar dari client')
        print()
        print('  Perintah dari operator (otomatis):')
        print('  ─────────────────────────────────────')
        print('  🔒 lock       — Kunci layar PC')
        print('  🔓 unlock     — Buka kunci PC')
        print('  📷 screenshot — Ambil screenshot layar')
        print('  💬 message    — Tampilkan pesan')
        print('  ⏻  shutdown   — Matikan PC')
        print('  ⟳  restart    — Restart PC')
        print()

    def print_status(self):
        """Print current session status."""
        self.poll_status()  # Force refresh
        if self.current_session:
            s = self.current_session
            remaining = s.get('remaining_seconds', 0)
            mins = remaining // 60
            secs = remaining % 60
            print()
            print(f'  ╔══════════════════════════════════╗')
            print(f'  ║  Sesi Aktif                      ║')
            print(f'  ╠══════════════════════════════════╣')
            print(f'  ║  Nama    : {s.get("customer_name", "Guest"):<21s} ║')
            print(f'  ║  Durasi  : {str(s.get("duration_minutes", 0)) + " menit":<21s} ║')
            print(f'  ║  Sisa    : {f"{mins:02d}:{secs:02d}":<21s} ║')
            print(f'  ║  Mulai   : {s.get("start_time", "-"):<21s} ║')
            print(f'  ║  Selesai : {s.get("end_time", "-") or "-":<21s} ║')
            print(f'  ╠══════════════════════════════════╣')
            locked = '🔒 TERKUNCI' if self.lock_screen.is_visible() else '🔓 Tidak dikunci'
            print(f'  ║  Layar   : {locked:<21s} ║')
            print(f'  ╚══════════════════════════════════╝')
        else:
            print('\n  Tidak ada sesi aktif. PC tersedia.')
            if self.lock_screen.is_visible():
                print('  ⚠  Layar PC sedang TERKUNCI oleh operator.')

    def print_info(self):
        """Print client info."""
        print()
        print(f'  PC Name    : {self.pc_name}')
        print(f'  Server     : {self.base_url}')
        print(f'  IP         : {self.ip_address}')
        print(f'  MAC        : {self.mac_address}')
        print(f'  OS         : {platform.system()} {platform.release()}')
        print(f'  Lock Screen: {"✓ Tersedia (Tkinter)" if TK_AVAILABLE else "✗ Tidak tersedia"}')
        print(f'  Screenshot : {"✓ Tersedia (Pillow)" if PIL_AVAILABLE else "✗ Tidak tersedia"}')
        print(f'  Locked     : {"Ya" if self.lock_screen.is_visible() else "Tidak"}')
        print()


# ============================================================================
# Entry Point
# ============================================================================

def main():
    """Main entry point."""
    if not os.path.exists(CONFIG_FILE):
        print(f'[ERROR] Config file not found: {CONFIG_FILE}')
        print(f'[INFO] Buat file config.ini dengan format:')
        print(f'  [server]')
        print(f'  url = http://127.0.0.1:8000')
        print(f'  pc_name = PC-01')
        print(f'  ')
        print(f'  [client]')
        print(f'  heartbeat_interval = 5')
        print(f'  status_poll_interval = 3')
        print(f'  command_poll_interval = 3')
        print(f'  screenshot_quality = 60')
        sys.exit(1)

    client = WarnetProClient()
    client.start()


if __name__ == '__main__':
    main()
