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
        DB::table('settings')->whereIn('key', ['site_favicon', 'seo_image'])->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('settings')->insert([
            [
                'key' => 'site_favicon',
                'value' => null,
                'type' => 'string',
                'group' => 'general',
                'description' => 'Path to the site favicon',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'seo_image',
                'value' => null,
                'type' => 'string',
                'group' => 'seo',
                'description' => 'Default social media sharing image',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
};
