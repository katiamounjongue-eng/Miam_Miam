<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
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
            $table->string('sponsorship_code', 10)->unique();
            
            // Clés étrangères vers la table students
            $table->foreign('student_id')->references('user_id')->on('students')->onDelete('cascade');
            $table->foreign('godchild_id')->references('user_id')->on('students')->onDelete('set null');

            $table->timestamps();
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
