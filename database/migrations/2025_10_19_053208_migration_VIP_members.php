<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MigrationVIPMembers extends Migration
{
    public function up(): void
    {
        Schema::create('VIP_members', function (Blueprint $table) {
            $table->string('vip_id', 8)->primary();
            $table->string('user_id', 8);
            $table->date('vip_starting_date');
            $table->date('vip_ending_date');
            $table->timestamps();

            $table->foreign('user_id')
                  ->references('user_id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('VIP_members');
    }
};
