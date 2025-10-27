<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoyaltyPoints extends Migration
{
    public function up(): void
    {
        Schema::create('loyalty_points', function (Blueprint $table) {
            $table->string('loyalty_point_id', 8)->primary();
            $table->string('user_id', 8);
            $table->integer('points')->default(0)->comment('Positif = gain, Négatif = dépense');
            $table->dateTime('transaction_date');
            $table->timestamps();

            $table->foreign('user_id')
                  ->references('user_id')
                  ->on('users')
                  ->onDelete('cascade');
            
            // Indexes for better performance
            $table->index('user_id');
            $table->index('transaction_date');
            $table->index(['user_id', 'transaction_date']); // Composite index for user history queries
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loyalty_points');
    }
}