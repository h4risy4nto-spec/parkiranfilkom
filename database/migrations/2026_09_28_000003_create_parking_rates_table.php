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
        Schema::create('parking_rates', function (Blueprint $table) {
            $table->id();
            $table->string('vehicle_type', 30)->unique(); // Motor, Mobil
            $table->decimal('first_hour_rate', 10, 2)->default(2000);
            $table->decimal('hourly_rate', 10, 2)->default(2000);
            $table->decimal('lost_ticket_penalty', 10, 2)->default(25000);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parking_rates');
    }
};
