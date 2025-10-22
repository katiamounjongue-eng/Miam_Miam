<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->string('user_id', 8)->primary();
            $table->string('user_type_id', 8);
            $table->string('first_name');
            $table->string('last_name');
            $table->string('user_password', 15)->unique();
            $table->string('mail_adress')->unique();
            $table->string('phone_number', 12)->unique();
            $table->date('inscription_date');
            $table->boolean('account_statut');
            $table->rememberToken();
            $table->timestamps();

            $table->foreign('user_type_id')->references('user_type_id')->on('user_type')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
