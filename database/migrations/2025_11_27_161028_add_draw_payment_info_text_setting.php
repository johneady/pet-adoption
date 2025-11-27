<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('settings')->insert([
            [
                'key' => 'draw_payment_info_text',
                'value' => 'Online payments are currently not available. After submitting this request, please send an etransfer to xxx@xxxx.com, and Mary will confirm your tickets once the etransfer has been received. You should then receive an email confirmation of your ticket purchase.',
                'type' => 'string',
                'group' => 'fundraising',
                'description' => 'Message displayed to users when purchasing draw tickets',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('settings')->where('key', 'draw_payment_info_text')->delete();
    }
};
