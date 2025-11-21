<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // General Settings
            [
                'key' => 'site_name',
                'value' => 'Pet Adoption Center',
                'type' => 'string',
                'group' => 'general',
                'description' => 'The name of the website',
            ],
            [
                'key' => 'site_tagline',
                'value' => 'Find your perfect companion',
                'type' => 'string',
                'group' => 'general',
                'description' => 'The tagline or slogan for the website',
            ],
            [
                'key' => 'site_description',
                'value' => 'We help connect loving families with pets in need of homes.',
                'type' => 'string',
                'group' => 'general',
                'description' => 'A brief description of the website',
            ],
            [
                'key' => 'site_logo',
                'value' => null,
                'type' => 'string',
                'group' => 'general',
                'description' => 'Path to the site logo',
            ],
            [
                'key' => 'site_favicon',
                'value' => null,
                'type' => 'string',
                'group' => 'general',
                'description' => 'Path to the site favicon',
            ],
            [
                'key' => 'default_timezone',
                'value' => 'America/Toronto',
                'type' => 'string',
                'group' => 'general',
                'description' => 'Default timezone for the application',
            ],

            // SEO Settings
            [
                'key' => 'seo_title',
                'value' => 'Pet Adoption Center - Find Your Perfect Companion',
                'type' => 'string',
                'group' => 'seo',
                'description' => 'Default SEO title for the website',
            ],
            [
                'key' => 'seo_description',
                'value' => 'Browse our selection of adorable pets looking for loving homes. Adopt a dog, cat, or other pet today.',
                'type' => 'string',
                'group' => 'seo',
                'description' => 'Default SEO meta description',
            ],
            [
                'key' => 'seo_keywords',
                'value' => 'pet adoption, adopt a dog, adopt a cat, animal rescue, pet shelter',
                'type' => 'string',
                'group' => 'seo',
                'description' => 'Default SEO keywords',
            ],
            [
                'key' => 'seo_image',
                'value' => null,
                'type' => 'string',
                'group' => 'seo',
                'description' => 'Default social media sharing image',
            ],

            // Contact Settings
            [
                'key' => 'contact_email',
                'value' => 'info@petadoption.test',
                'type' => 'string',
                'group' => 'contact',
                'description' => 'Primary contact email address',
            ],
            [
                'key' => 'contact_phone',
                'value' => null,
                'type' => 'string',
                'group' => 'contact',
                'description' => 'Primary contact phone number',
            ],
            [
                'key' => 'contact_address',
                'value' => null,
                'type' => 'string',
                'group' => 'contact',
                'description' => 'Physical address of the organization',
            ],
            [
                'key' => 'business_hours',
                'value' => json_encode([
                    'monday' => '9:00 AM - 5:00 PM',
                    'tuesday' => '9:00 AM - 5:00 PM',
                    'wednesday' => '9:00 AM - 5:00 PM',
                    'thursday' => '9:00 AM - 5:00 PM',
                    'friday' => '9:00 AM - 5:00 PM',
                    'saturday' => '10:00 AM - 4:00 PM',
                    'sunday' => 'Closed',
                ]),
                'type' => 'json',
                'group' => 'contact',
                'description' => 'Business operating hours',
            ],

            // Social Media Settings
            [
                'key' => 'social_facebook',
                'value' => null,
                'type' => 'string',
                'group' => 'social',
                'description' => 'Facebook page URL',
            ],
            [
                'key' => 'social_twitter',
                'value' => null,
                'type' => 'string',
                'group' => 'social',
                'description' => 'Twitter/X profile URL',
            ],
            [
                'key' => 'social_instagram',
                'value' => null,
                'type' => 'string',
                'group' => 'social',
                'description' => 'Instagram profile URL',
            ],
            [
                'key' => 'social_youtube',
                'value' => null,
                'type' => 'string',
                'group' => 'social',
                'description' => 'YouTube channel URL',
            ],
            [
                'key' => 'social_linkedin',
                'value' => null,
                'type' => 'string',
                'group' => 'social',
                'description' => 'LinkedIn profile URL',
            ],

            // Application Settings
            [
                'key' => 'enable_draws',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'application',
                'description' => 'Enable or disable 50/50 draws',
            ],
            [
                'key' => 'enable_memberships',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'application',
                'description' => 'Enable or disable memberships',
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
