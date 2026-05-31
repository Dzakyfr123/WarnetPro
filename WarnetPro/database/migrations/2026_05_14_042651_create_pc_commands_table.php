<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pc_commands', function (Blueprint $table) {
            $table->id();
            $table->foreignId('computer_id')->constrained()->onDelete('cascade');
            $table->enum('command_type', ['shutdown', 'restart', 'message']);
            $table->text('payload')->nullable();
            $table->enum('status', ['pending', 'acknowledged'])->default('pending');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pc_commands');
    }
};
