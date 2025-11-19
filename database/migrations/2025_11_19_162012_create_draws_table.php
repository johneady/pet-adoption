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
        Schema::create('draws', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->json('ticket_price_tiers');
            $table->dateTime('starts_at');
            $table->dateTime('ends_at');
            $table->boolean('is_finalized')->default(false);
            $table->unsignedBigInteger('winner_ticket_id')->nullable();
            $table->timestamps();

            $table->index(['starts_at', 'ends_at']);
            $table->index('is_finalized');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('draws');
    }
};
