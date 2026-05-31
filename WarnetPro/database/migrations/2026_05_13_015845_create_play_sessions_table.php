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
       Schema::create('play_sessions', function (Blueprint $table) {
    $table->id();

    $table->foreignId('computer_id')
        ->constrained()
        ->onDelete('cascade');

    $table->foreignId('booking_id')
        ->nullable()
        ->constrained()
        ->onDelete('set null');

    $table->string('customer_name');

    $table->dateTime('start_time');
    $table->dateTime('end_time')->nullable();

    $table->integer('duration_minutes');
    $table->integer('remaining_minutes');

    $table->enum('status', [
        'playing',
        'finished'
    ])->default('playing');

    $table->foreignId('created_by')
        ->constrained('users')
        ->onDelete('cascade');

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('play_sessions');
    }
};
