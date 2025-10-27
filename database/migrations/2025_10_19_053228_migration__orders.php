<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MigrationOrders extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->string('order_id', 8)->primary();
            $table->string('user_id', 8);
            $table->string('localisation_id', 8);
            $table->string('order_statut_id', 8);
            $table->date('order_date');
            $table->timestamps();

            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('localisation_id')->references('localisation_id')->on('localisation')->onDelete('cascade');
            $table->foreign('order_statut_id')->references('order_statut_id')->on('order_statut')->onDelete('cascade');
            
            $table->index('order_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
