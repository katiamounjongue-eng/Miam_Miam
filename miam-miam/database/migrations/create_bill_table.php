<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('Bill', function (Blueprint $table) {
            $table->string('bill_id', 8)->primary(); 
            
           
            $table->string('order_id', 8)->unique(); 
            $table->foreign('order_id')->references('order_id')->on('Orders');

            $table->string('payment_method_id', 8); 
            $table->foreign('payment_method_id')->references('payment_method_id')->on('Payment');

            $table->decimal('total_cost', 19, 2);
            $table->dateTime('payment_date'); //  pour la traçabilité de la transaction

            $table->timestamps();
        });
    }

   
    public function down(): void
    {
        Schema::dropIfExists('Bill');
    }
};