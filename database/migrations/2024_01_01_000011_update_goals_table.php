<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('goals', function (Blueprint $table) {
            $table->string('type')->change();
            $table->decimal('target_value', 8, 2)->nullable()->after('type');
            $table->decimal('starting_value', 8, 2)->nullable()->after('target_value');
            $table->decimal('current_value', 8, 2)->nullable()->after('starting_value');
            $table->unsignedInteger('target_sessions')->nullable()->after('current_value');
            $table->unsignedInteger('current_sessions')->default(0)->after('target_sessions');
            $table->foreignId('exercise_id')->nullable()->after('current_sessions')
                  ->constrained('exercises')->onDelete('set null');
            $table->enum('status', ['pending', 'active', 'achieved', 'cancelled'])
                  ->default('pending')->change();
        });
    }

    public function down(): void
    {
        Schema::table('goals', function (Blueprint $table) {
            $table->dropColumn([
                'target_value', 'starting_value', 'current_value',
                'target_sessions', 'current_sessions',
            ]);
            $table->dropForeign(['exercise_id']);
            $table->dropColumn('exercise_id');
        });
    }
};