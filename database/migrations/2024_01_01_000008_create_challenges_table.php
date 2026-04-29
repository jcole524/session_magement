<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('challenges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['session_count', 'total_weight', 'streak', 'specific_exercise']);
            $table->unsignedInteger('target');                    // e.g. 10 sessions, 1000 kg, 7 days
            $table->foreignId('exercise_id')->nullable()->constrained('exercises')->onDelete('set null');
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['upcoming', 'open', 'closed'])->default('upcoming');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('challenges');
    }
};
