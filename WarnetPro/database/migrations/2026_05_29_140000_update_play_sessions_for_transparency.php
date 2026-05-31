<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Check if table already exists
        if (!Schema::hasTable('play_sessions')) {
            Schema::create('play_sessions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('computer_id')->constrained();
                $table->foreignId('member_id')->nullable()->constrained();
                $table->enum('status', ['pending', 'playing', 'ended'])->default('pending');
                
                // Waktu pakai (dalam menit)
                $table->integer('duration_minutes')->nullable();
                
                // Timestamps tracking
                $table->timestamp('started_at')->nullable();
                $table->timestamp('ended_at')->nullable();
                $table->timestamp('last_heartbeat')->nullable();
                
                // Cost tracking
                $table->decimal('rate_per_minute', 8, 2)->default(0);
                $table->decimal('total_cost', 10, 2)->default(0);
                
                // Anti-fraud tracking
                $table->ipAddress('client_ip_address')->nullable();
                $table->string('client_mac_address')->nullable();
                $table->json('activity_log')->nullable();
                $table->boolean('is_suspicious')->default(false);
                
                $table->timestamps();
                $table->softDeletes();
            });
        } else {
            // Add columns if they don't exist
            Schema::table('play_sessions', function (Blueprint $table) {
                if (!Schema::hasColumn('play_sessions', 'last_heartbeat')) {
                    $table->timestamp('last_heartbeat')->nullable();
                }
                if (!Schema::hasColumn('play_sessions', 'client_ip_address')) {
                    $table->ipAddress('client_ip_address')->nullable();
                }
                if (!Schema::hasColumn('play_sessions', 'client_mac_address')) {
                    $table->string('client_mac_address')->nullable();
                }
                if (!Schema::hasColumn('play_sessions', 'activity_log')) {
                    $table->json('activity_log')->nullable();
                }
                if (!Schema::hasColumn('play_sessions', 'is_suspicious')) {
                    $table->boolean('is_suspicious')->default(false);
                }
            });
        }
    }

    public function down(): void
    {
        Schema::table('play_sessions', function (Blueprint $table) {
            if (Schema::hasColumn('play_sessions', 'last_heartbeat')) {
                $table->dropColumn('last_heartbeat');
            }
            if (Schema::hasColumn('play_sessions', 'client_ip_address')) {
                $table->dropColumn('client_ip_address');
            }
            if (Schema::hasColumn('play_sessions', 'client_mac_address')) {
                $table->dropColumn('client_mac_address');
            }
            if (Schema::hasColumn('play_sessions', 'activity_log')) {
                $table->dropColumn('activity_log');
            }
            if (Schema::hasColumn('play_sessions', 'is_suspicious')) {
                $table->dropColumn('is_suspicious');
            }
        });
    }
};
