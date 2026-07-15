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
        Schema::create('staff_leaves', function (Blueprint $table) {
            $table->id();

            $table->foreignId('staff_id')
                ->constrained('staff_profiles')
                ->cascadeOnDelete();

            $table->date('start_date');
            $table->date('end_date');

            $table->enum('leave_type', [
                'annual',
                'sick',
                'emergency',
                'unpaid',
                'other',
            ]);

            $table->text('reason')->nullable();

            $table->enum('status', [
                'pending',
                'approved',
                'rejected',
            ])->default('pending');

            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('approved_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_leaves');
    }
};