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
        Schema::create('membership_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Bronze, Silver, Gold
            $table->string('slug')->unique();
            $table->decimal('price', 10, 2);
            $table->string('stripe_price_id')->nullable();
            $table->text('description')->nullable();
            $table->json('features')->nullable();
            $table->string('badge_color')->default('#94a3b8'); // Tailwind slate-400
            $table->string('badge_icon')->default('star');
            $table->unsignedInteger('display_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['is_active', 'display_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('membership_plans');
    }
};
