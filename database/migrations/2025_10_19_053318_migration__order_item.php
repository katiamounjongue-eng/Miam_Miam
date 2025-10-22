<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_item', function (Blueprint $table) {
            $table->string('order_item_id', 8)->primary();
            $table->string('item_id', 8);
            $table->string('order_id', 8);
            $table->integer('item_quantity');
            $table->timestamps();

            $table->foreign('item_id')->references('item_id')->on('item')->onDelete('cascade');
            $table->foreign('order_id')->references('order_id')->on('orders')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_item');
    }
};
