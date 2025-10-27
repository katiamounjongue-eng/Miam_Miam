<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MigrationOrderHistoric extends Migration
{
   
    public function up(): void
    {
        Schema::create('order_historic', function (Blueprint $table) {
            $table->string('historic_id', 8)->primary();
            $table->string('order_id', 8);
            $table->string('user_id', 8);
            $table->timestamps();

            $table->foreign('order_id')->references('order_id')->on('orders')->onDelete('cascade');
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
            
            // Add indexes for better performance
            $table->index('order_id');
            $table->index('user_id');
        }); 
                
    }

   
    public function down(): void
    {
        Schema::dropIfExists('Order_Historic');
    }
};