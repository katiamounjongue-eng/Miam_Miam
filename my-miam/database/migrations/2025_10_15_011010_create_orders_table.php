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
        if (Schema::hasTable('orders')) {
            return;
        }

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // L'étudiant qui commande
            $table->decimal('total_amount', 8, 2);
            $table->decimal('discount_used', 8, 2)->default(0); // Points de fidélité ou réduction appliquée
            $table->enum('status', ['pending', 'processing', 'delivered', 'rejected', 'completed'])->default('pending');
            $table->enum('type', ['delivery', 'onsite'])->default('delivery');
            $table->string('delivery_address'); // Utilisera le champ 'localisation' de l'utilisateur par défaut
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
