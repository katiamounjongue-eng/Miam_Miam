<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MigrationPayment extends Migration
{
    
    public function up(): void
    {
        Schema::create('payment', function (Blueprint $table) {
        $table->string('payment_method_id', 8)->primary();
        $table->string('method_name', 50)->unique();
        $table->string('description', 255)->nullable();
        $table->timestamps();
        });
    }

    
    public function down(): void
    {
        Schema::dropIfExists('Payment');
    }
};