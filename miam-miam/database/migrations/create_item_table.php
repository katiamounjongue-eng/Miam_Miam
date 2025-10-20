<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('Item', function (Blueprint $table) {
            $table->string('item_id', 8)->primary();

           
            $table->string('item_type_id', 8); 
            $table->foreign('item_type_id')->references('item_type_id')->on('Item_type');

            $table->string('name', 255)->unique(); 
            $table->string('description', 255); 
            $table->unsignedInteger('quantity')->default(0); 
            $table->decimal('price', 19, 2); 
            $table->string('image_link', 255)->unique(); 

            $table->timestamps(); 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('Item');
    }
};