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
        Schema::create('draw_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('draw_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('ticket_number');
            $table->boolean('is_winner')->default(false);
            $table->timestamps();

            $table->unique(['draw_id', 'ticket_number']);
            $table->index(['draw_id', 'user_id']);
            $table->index('is_winner');
        });

        // Note: Foreign key constraint for winner_ticket_id is not added for SQLite compatibility.
        // The relationship is enforced at the application level.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('draw_tickets');
    }
};
