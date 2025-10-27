<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComplaintsTable extends Migration  // Au lieu de MigrationComplaint
{
    public function up(): void
    {
        Schema::create('complaints', function (Blueprint $table) {
            $table->string('complaint_id', 8)->primary();
            $table->string('user_id', 8);
            $table->string('order_id', 8)->nullable();
            $table->enum('complaint_type', ['order', 'delivery', 'quality', 'payment', 'technical', 'other']);
            $table->string('subject', 255);
            $table->text('description');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->enum('status', ['pending', 'in_progress', 'resolved', 'closed'])->default('pending');
            $table->text('resolution_note')->nullable();
            $table->dateTime('resolved_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')
                ->references('user_id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('order_id')
                ->references('order_id')
                ->on('orders')
                ->onDelete('set null');

            // Index pour améliorer les performances des requêtes
            $table->index('status');
            $table->index('complaint_type');
            $table->index('priority');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
}