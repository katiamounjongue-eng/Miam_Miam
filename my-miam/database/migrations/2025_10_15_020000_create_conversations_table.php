<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('conversations')) {
            return;
        }

        Schema::create('conversations', function (Blueprint $table) {
            $table->id();

            // Le client (Étudiant) qui initie la conversation
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Le dernier utilisateur (Employé/Admin) qui a répondu
            $table->foreignId('last_message_by')->nullable()->constrained('users')->nullOnDelete();

            $table->string('subject')->nullable();
            $table->boolean('is_read_customer')->default(true);
            $table->boolean('is_read_support')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
