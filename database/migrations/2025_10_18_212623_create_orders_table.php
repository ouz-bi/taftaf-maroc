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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('address_id')->constrained();
            $table->foreignId('rider_id')->nullable()->constrained('riders');

            // Montants
            $table->decimal('subtotal', 10, 2);
            $table->decimal('delivery_fee', 8, 2)->default(0);
            $table->decimal('discount', 8, 2)->default(0);
            $table->decimal('total', 10, 2);

            // Statut
            $table->enum('status', [
                'pending',      // En attente
                'confirmed',    // Confirmée
                'preparing',    // En préparation
                'ready',        // Prête
                'picked_up',    // Récupérée par livreur
                'on_the_way',   // En route
                'delivered',    // Livrée
                'cancelled'     // Annulée
            ])->default('pending');

            // Paiement
            $table->enum('payment_method', ['cash', 'card', 'cmi', 'cashplus'])->default('cash');
            $table->enum('payment_status', ['pending', 'paid', 'failed'])->default('pending');
            $table->string('payment_reference')->nullable();

            // Timing
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->integer('delivery_time')->nullable(); // Temps réel en minutes

            $table->text('notes')->nullable();
            $table->text('cancellation_reason')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
