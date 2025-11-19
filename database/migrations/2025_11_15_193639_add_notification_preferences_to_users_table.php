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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('receive_new_user_alerts')->default(true)->after('is_admin');
            $table->boolean('receive_new_adoption_alerts')->default(true)->after('receive_new_user_alerts');
            $table->boolean('receive_draw_result_alerts')->default(true)->after('receive_new_adoption_alerts');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['receive_new_user_alerts', 'receive_new_adoption_alerts', 'receive_draw_result_alerts']);
        });
    }
};
