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
        Schema::create('staff_availabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained('staff_profiles')->onDelete('cascade');
            $table->enum('day_of_week', ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun']);
            $table->time('start_time');
            $table->time('end_time');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_availabilities');
    }
};
