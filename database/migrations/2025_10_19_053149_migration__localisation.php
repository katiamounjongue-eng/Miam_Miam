<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Migrationlocalisation extends Migration
{
    public function up(): void
    {
        Schema::create('localisation', function (Blueprint $table) {
            $table->string('localisation_id', 8)->primary();
            $table->string('localisation_name')->unique();
            $table->decimal('localisation_delivery_price', 19, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('localisation');
    }
};
