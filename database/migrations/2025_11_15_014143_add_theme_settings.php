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
                'key' => 'theme_preset',
                'value' => 'navy-gold',
                'type' => 'string',
                'group' => 'theme',
                'description' => 'The color theme preset for the frontend',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'theme_primary_color',
                'value' => null,
                'type' => 'string',
                'group' => 'theme',
                'description' => 'Custom primary color (overrides preset if set)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'theme_secondary_color',
                'value' => null,
                'type' => 'string',
                'group' => 'theme',
                'description' => 'Custom secondary color (overrides preset if set)',
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
        DB::table('settings')->whereIn('key', [
            'theme_preset',
            'theme_primary_color',
            'theme_secondary_color',
        ])->delete();
    }
};
