<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MigrationitemType extends Migration
{
   
    public function up(): void
    {
        Schema::create('item_type', function (Blueprint $table) {
            $table->string('item_type_id', 8)->primary(); 
            $table->string('item_type_name', 255)->unique(); 
        });
    }

    
    public function down(): void
    {
        Schema::dropIfExists('item_type');
    }
};