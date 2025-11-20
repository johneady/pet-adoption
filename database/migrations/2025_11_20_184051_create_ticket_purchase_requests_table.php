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
        Schema::create('ticket_purchase_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('draw_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('quantity');
            $table->json('pricing_tier');
            $table->string('status')->default('pending'); // pending, fulfilled, cancelled
            $table->timestamps();

            $table->index(['draw_id', 'user_id']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_purchase_requests');
    }
};
