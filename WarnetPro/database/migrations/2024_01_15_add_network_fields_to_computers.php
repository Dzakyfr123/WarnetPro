<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('computers', function (Blueprint $table) {
            // Add columns if they don't exist
            if (!Schema::hasColumn('computers', 'hostname')) {
                $table->string('hostname')->nullable()->after('pc_name');
            }
            if (!Schema::hasColumn('computers', 'ip_address')) {
                $table->string('ip_address')->nullable()->after('hostname');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('computers', function (Blueprint $table) {
            $table->dropColumnIfExists('hostname');
            $table->dropColumnIfExists('ip_address');
        });
    }
};
