<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exercises', function (Blueprint $table) {
            $table->enum('difficulty', ['beginner', 'intermediate', 'advanced'])
                  ->default('beginner')->after('muscle_group');
            $table->string('equipment')->nullable()->after('difficulty');
            $table->text('instructions')->nullable()->after('equipment');
        });
    }

    public function down(): void
    {
        Schema::table('exercises', function (Blueprint $table) {
            $table->dropColumn(['difficulty', 'equipment', 'instructions']);
        });
    }
};