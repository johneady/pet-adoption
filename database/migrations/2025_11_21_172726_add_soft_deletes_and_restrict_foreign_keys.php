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
        // Add soft deletes to users, pets, and blog_posts
        Schema::table('users', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('pets', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('blog_posts', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Change species_id foreign key from CASCADE to RESTRICT
        Schema::table('pets', function (Blueprint $table) {
            $table->dropForeign(['species_id']);
            $table->foreign('species_id')
                ->references('id')
                ->on('species')
                ->restrictOnDelete();
        });

        // Change breed_id foreign key to RESTRICT
        Schema::table('pets', function (Blueprint $table) {
            $table->dropForeign(['breed_id']);
            $table->foreign('breed_id')
                ->references('id')
                ->on('breeds')
                ->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore original foreign keys
        Schema::table('pets', function (Blueprint $table) {
            $table->dropForeign(['breed_id']);
            $table->foreign('breed_id')
                ->references('id')
                ->on('breeds')
                ->nullOnDelete();
        });

        Schema::table('pets', function (Blueprint $table) {
            $table->dropForeign(['species_id']);
            $table->foreign('species_id')
                ->references('id')
                ->on('species')
                ->cascadeOnDelete();
        });

        // Remove soft deletes
        Schema::table('blog_posts', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('pets', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
