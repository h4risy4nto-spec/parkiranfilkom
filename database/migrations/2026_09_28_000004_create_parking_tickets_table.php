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
        Schema::create('parking_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_code', 50)->unique()->index();
            $table->string('plate_number', 25)->index();
            $table->enum('vehicle_type', ['Motor', 'Mobil']);
            $table->foreignId('parking_area_id')->constrained('parking_areas')->cascadeOnDelete();
            $table->foreignId('parking_slot_id')->nullable()->constrained('parking_slots')->nullOnDelete();
            $table->timestamp('entry_time');
            $table->timestamp('exit_time')->nullable();
            $table->unsignedInteger('duration_minutes')->default(0);
            $table->decimal('hourly_rate', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->enum('status', ['active', 'paid', 'lost'])->default('active')->index();
            $table->foreignId('operator_in_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('operator_out_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parking_tickets');
    }
};
