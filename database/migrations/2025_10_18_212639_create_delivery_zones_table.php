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
        Schema::create('delivery_zones', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Maarif, Ain Diab, etc.
            $table->string('city'); // Casablanca, Rabat
            $table->json('polygon_coordinates'); // Coordonnées de la zone
            $table->decimal('delivery_fee', 8, 2)->default(0);
            $table->integer('estimated_time')->default(20); // En minutes
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_zones');
    }
};
