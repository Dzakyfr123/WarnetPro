<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('computers', function (Blueprint $table) {
            $table->timestamp('last_heartbeat')->nullable()->after('status');
            $table->string('ip_address')->nullable()->after('last_heartbeat');
            $table->string('mac_address')->nullable()->after('ip_address');
        });
    }

    public function down(): void
    {
        Schema::table('computers', function (Blueprint $table) {
            $table->dropColumn(['last_heartbeat', 'ip_address', 'mac_address']);
        });
    }
};
