<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('Payment', function (Blueprint $table) {
            $table->string('payment_method_id', 8)->primary(); 
            $table->string('method_name', 30)->unique(); 
            
            
        });
    }

    
    public function down(): void
    {
        Schema::dropIfExists('Payment');
    }
};