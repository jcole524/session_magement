<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('session_exercises', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('workout_sessions')->onDelete('cascade');
            $table->foreignId('exercise_id')->constrained('exercises')->onDelete('cascade');
            $table->unsignedTinyInteger('sets')->nullable();
            $table->unsignedSmallInteger('reps')->nullable();
            $table->decimal('weight_kg', 6, 2)->nullable();
            $table->unsignedSmallInteger('duration_mins')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('session_exercises');
    }
};