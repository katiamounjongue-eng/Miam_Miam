<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('Special_Event', function (Blueprint $table) {
            $table->string('event_id', 8)->primary(); 

            
            $table->string('event_type_id', 8); 
            $table->foreign('event_type_id')->references('event_type_id')->on('Event_Type');

            $table->string('event_name', 255)->unique(); 
            $table->date('event_starting_date');
            $table->date('event_ending_date'); 
            $table->string('event_description', 255)->nullable(); 
            
            $table->timestamps();
        });
    }

    
    public function down(): void
    {
        Schema::dropIfExists('Special_Event');
    }
};