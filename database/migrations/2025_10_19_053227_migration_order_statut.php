<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MigrationOrderStatut extends Migration
{
    public function up(): void
    {
        Schema::create('order_statut', function (Blueprint $table) {
            $table->string('order_statut_id', 8)->primary();
            $table->string('order_statut_name', 35)->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_statut');
    }
};
