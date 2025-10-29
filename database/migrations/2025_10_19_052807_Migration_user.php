<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MigrationUser extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->string('user_id', 8)->primary();
            $table->string('user_type_id', 8);
            $table->string('first_name');
            $table->string('last_name');
            // ✅ CORRIGÉ : 255 pour stocker le hash bcrypt (60+ chars)
            $table->string('password', 255);
            $table->string('mail_adress')->unique()->nullable();
            $table->string('phone_number', 12)->unique()->nullable();
            $table->date('inscription_date');
            $table->boolean('account_statut')->default(true);
            $table->timestamp('email_verified_at')->nullable();
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
