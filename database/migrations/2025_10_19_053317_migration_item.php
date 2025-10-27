<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Migrationitem extends Migration
{
    public function up(): void
    {
        Schema::create('item', function (Blueprint $table) {
            $table->string('item_id', 8)->primary();
            $table->string('item_type_id', 8);
            $table->string('name', 255)->unique();
            $table->string('description', 255);
            $table->unsignedInteger('quantity')->default(0);
            $table->decimal('price', 19, 2);
            $table->string('image_link', 255)->nullable();
            
            // ✅ AJOUTÉ : Champs additionnels pour le menu
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_available')->default(true);
            $table->integer('preparation_time')->nullable()->comment('Temps en minutes');
            $table->integer('calories')->nullable();
            $table->string('allergens', 255)->nullable();
            $table->text('ingredients')->nullable();
            
            $table->timestamps();

            $table->foreign('item_type_id')->references('item_type_id')->on('item_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item');
    }
}