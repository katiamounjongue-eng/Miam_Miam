<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MigrationEventType extends Migration
{
    
    public function up(): void
    {
        Schema::create('event_type', function (Blueprint $table) {
            $table->string('event_type_id', 8)->primary(); 
            $table->string('event_type_name', 255)->unique(); 
        });
    }

    
    public function down(): void
    {
        Schema::dropIfExists('event_type');
    }
};