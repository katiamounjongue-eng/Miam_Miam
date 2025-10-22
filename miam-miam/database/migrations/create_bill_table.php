<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('Bill', function (Blueprint $table) {
            $table->char('bill_id', 8)->primary(); 
            
            // CORRECTION 1: Utilisation de char(8) pour correspondre au type de clé primaire
            // Cela garantit que la colonne correspond exactement à la définition de order_id dans Orders.
            $table->char('order_id', 8)->unique(); // Reste unique, car une Order n'a qu'une Bill
            
            $table->foreign('order_id')->references('order_id')->on('Orders')
                  ->onUpdate('cascade')->onDelete('cascade'); // Bonne pratique d'ajouter CASCADE

            // CORRECTION 2: Utilisation de char(8) pour 'payment_method_id' aussi
            $table->char('payment_method_id', 8); 
            $table->foreign('payment_method_id')->references('payment_method_id')->on('Payment')
                  ->onUpdate('cascade')->onDelete('restrict');

            $table->decimal('total_cost', 19, 2);
            $table->dateTime('payment_date'); 

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('Bill');
    }
};