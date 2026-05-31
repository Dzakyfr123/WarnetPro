<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Computer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin user
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@warnet.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        // Create Operator user
        User::create([
            'name' => 'Operator',
            'email' => 'operator@warnet.com',
            'password' => bcrypt('password'),
            'role' => 'operator',
        ]);

        // Create 10 PCs
        for ($i = 1; $i <= 10; $i++) {
            Computer::create([
                'pc_name' => 'PC-' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'status' => 'available',
            ]);
        }
    }
}
