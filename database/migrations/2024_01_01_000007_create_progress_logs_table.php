<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('progress_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('session_id')->nullable()->constrained('workout_sessions')->onDelete('set null');
            $table->decimal('body_weight_kg', 5, 2)->nullable();
            $table->text('notes')->nullable();
            $table->date('log_date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('progress_logs');
    }
};