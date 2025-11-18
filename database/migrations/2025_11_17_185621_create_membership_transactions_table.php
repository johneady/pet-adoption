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
        Schema::create('membership_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('membership_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['payment', 'refund']);
            $table->decimal('amount', 10, 2);
            $table->string('payment_method')->nullable();
            $table->string('paypal_txn_id')->nullable();
            $table->enum('status', ['pending', 'completed', 'failed'])->default('pending');
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete(); // Admin who processed refund
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['membership_id', 'type']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('membership_transactions');
    }
};
