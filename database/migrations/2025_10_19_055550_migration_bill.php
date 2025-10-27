<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MigrationBill extends Migration
{
    
    public function up(): void
    {
        Schema::create('bill', function (Blueprint $table) {
            $table->string('bill_id', 8)->primary();
            $table->string('order_id', 8)->unique();
            $table->string('payment_method_id', 8);
            $table->decimal('total_cost', 19, 2);
            // ✅ payment_date est ICI dans Bill, pas dans Payment
            $table->dateTime('payment_date')->nullable();
            $table->timestamps();

            $table->foreign('order_id')->references('order_id')->on('orders')->onDelete('cascade');
            $table->foreign('payment_method_id')->references('payment_method_id')->on('payment');
        });
        
    }

   
    public function down(): void
    {
        Schema::dropIfExists('Bill');
    }
};