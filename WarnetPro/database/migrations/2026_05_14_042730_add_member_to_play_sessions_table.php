<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('play_sessions', function (Blueprint $table) {
            $table->foreignId('member_id')->nullable()->after('booking_id')
                ->constrained('members')->onDelete('set null');
            // Make customer_name nullable for guest sessions (no name needed)
            $table->string('customer_name')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('play_sessions', function (Blueprint $table) {
            $table->dropForeign(['member_id']);
            $table->dropColumn('member_id');
            $table->string('customer_name')->nullable(false)->change();
        });
    }
};
