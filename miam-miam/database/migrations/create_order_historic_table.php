<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   
    public function up(): void
    {
        Schema::create('Order_Historic', function (Blueprint $table) {
            $table->string('historic_id', 8)->primary(); 
            
            
            $table->string('order_id', 8)->unique(); 
            $table->foreign('order_id')->references('order_id')->on('Orders');
            
            $table->string('user_id', 8); 
            $table->foreign('user_id')->references('user_id')->on('Users');

            $table->timestamps(); 
        });
    }

   
    public function down(): void
    {
        Schema::dropIfExists('Order_Historic');
    }
};