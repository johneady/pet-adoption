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
        Schema::create('adoption_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('pet_id')->constrained()->cascadeOnDelete();
            $table->enum('status', [
                'submitted',
                'under_review',
                'interview_scheduled',
                'approved',
                'rejected',
                'completed',
            ])->default('submitted');
            $table->string('living_situation');
            $table->text('experience')->nullable();
            $table->text('other_pets')->nullable();
            $table->string('veterinary_reference')->nullable();
            $table->text('household_members')->nullable();
            $table->string('employment_status')->nullable();
            $table->text('reason_for_adoption');
            $table->text('admin_notes')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['pet_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('adoption_applications');
    }
};
