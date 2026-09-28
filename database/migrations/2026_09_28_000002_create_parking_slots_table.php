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
        Schema::create('parking_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parking_area_id')->constrained('parking_areas')->cascadeOnDelete();
            $table->unsignedInteger('slot_number');
            $table->string('slot_code', 30); // e.g., "A-01", "B-12"
            $table->boolean('is_occupied')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['parking_area_id', 'slot_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parking_slots');
    }
};
