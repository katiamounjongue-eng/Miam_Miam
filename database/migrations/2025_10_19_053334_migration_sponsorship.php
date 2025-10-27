<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MigrationSponsorship extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sponsorships', function (Blueprint $table) {
            $table->string('sponsorship_relation_id', 8)->primary();
            $table->string('student_id', 8);
            $table->string('godchild_id', 8)->nullable();
            // ✅ CORRIGÉ : sponsorship_code au lieu de sponsordhip_code
            $table->string('sponsorship_code', 10)->unique()->nullable();
            $table->timestamps();

            $table->foreign('student_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('godchild_id')->references('user_id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sponsorships');
    }
};
