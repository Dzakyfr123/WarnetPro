<?php

namespace App\Http\Controllers;

use App\Models\Computer;
use Illuminate\Http\Request;

class NetworkScannerController extends Controller
{
    /**
     * Display network scanner page
     */
    public function index()
    {
        $computers = Computer::all();
        return view('network_scanner_index', compact('computers'));
    }

    /**
     * Scan network dan return IP list
     */
    public function scanNetwork()
    {
        try {
            $ipList = $this->getNetworkIPs();
            $activePCs = $this->pingIPs($ipList);
            
            return response()->json([
                'success' => true,
                'data' => $activePCs,
                'total' => count($activePCs),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get network IP range
     */
    private function getNetworkIPs()
    {
        $ips = [];
        
        // Windows: gunakan ipconfig
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $output = shell_exec('ipconfig');
            
            // Parse output untuk cari subnet
            if (preg_match('/IPv4 Address[\s\.]+: (\d+\.\d+\.\d+\.\d+)/', $output, $matches)) {
                $serverIP = $matches[1];
                
                // Get network range (contoh: 192.168.1.x)
                $parts = explode('.', $serverIP);
                $network = $parts[0] . '.' . $parts[1] . '.' . $parts[2];
                
                // Generate IP range 1-254
                for ($i = 1; $i <= 254; $i++) {
                    $ips[] = $network . '.' . $i;
                }
            }
        } 
        // Linux/Mac: gunakan ifconfig
        else {
            $output = shell_exec('ifconfig');
            if (preg_match('/inet (\d+\.\d+\.\d+\.\d+)/', $output, $matches)) {
                $serverIP = $matches[1];
                $parts = explode('.', $serverIP);
                $network = $parts[0] . '.' . $parts[1] . '.' . $parts[2];
                
                for ($i = 1; $i <= 254; $i++) {
                    $ips[] = $network . '.' . $i;
                }
            }
        }
        
        return $ips;
    }

    /**
     * Ping multiple IPs
     */
    private function pingIPs($ips, $timeout = 1)
    {
        $activePCs = [];
        
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            // Windows ping
            foreach ($ips as $ip) {
                $result = @shell_exec("ping -n 1 -w 500 $ip");
                if (strpos($result, 'reply from') !== false) {
                    $hostname = @gethostbyaddr($ip);
                    $registeredPC = Computer::where('ip_address', $ip)->first();
                    
                    $activePCs[] = [
                        'ip' => $ip,
                        'hostname' => $hostname !== $ip ? $hostname : 'Unknown',
                        'status' => $registeredPC ? 'registered' : 'new',
                        'pc_id' => $registeredPC ? $registeredPC->id : null,
                        'pc_name' => $registeredPC ? $registeredPC->pc_name : null,
                    ];
                }
            }
        } else {
            // Linux/Mac ping
            foreach ($ips as $ip) {
                $result = @shell_exec("ping -c 1 -W 1 $ip 2>/dev/null");
                if (strpos($result, 'bytes from') !== false) {
                    $hostname = @gethostbyaddr($ip);
                    $registeredPC = Computer::where('ip_address', $ip)->first();
                    
                    $activePCs[] = [
                        'ip' => $ip,
                        'hostname' => $hostname !== $ip ? $hostname : 'Unknown',
                        'status' => $registeredPC ? 'registered' : 'new',
                        'pc_id' => $registeredPC ? $registeredPC->id : null,
                        'pc_name' => $registeredPC ? $registeredPC->pc_name : null,
                    ];
                }
            }
        }
        
        return $activePCs;
    }

    /**
     * Register discovered PC
     */
    public function registerPC(Request $request)
    {
        $request->validate([
            'ip_address' => 'required|ip',
            'pc_name' => 'required|string|unique:computers,pc_name',
            'hostname' => 'nullable|string',
        ]);

        try {
            $computer = Computer::create([
                'ip_address' => $request->ip_address,
                'pc_name' => $request->pc_name,
                'hostname' => $request->hostname,
                'status' => 'available',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'PC registered successfully!',
                'data' => $computer,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get server IP address
     */
    public function getServerIP()
    {
        try {
            $serverIP = $this->getServerIPAddress();
            
            return response()->json([
                'success' => true,
                'ip' => $serverIP,
                'port' => env('APP_PORT', 8000),
                'url' => "http://$serverIP:" . env('APP_PORT', 8000),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get server IP address
     */
    private function getServerIPAddress()
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $output = shell_exec('ipconfig');
            if (preg_match('/IPv4 Address[\s\.]+: (\d+\.\d+\.\d+\.\d+)/', $output, $matches)) {
                return $matches[1];
            }
        } else {
            $output = shell_exec('ifconfig');
            if (preg_match('/inet (\d+\.\d+\.\d+\.\d+)/', $output, $matches)) {
                return $matches[1];
            }
        }
        
        return '127.0.0.1';
    }
}
