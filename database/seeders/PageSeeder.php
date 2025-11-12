<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $specialPages = [
            [
                'title' => 'Contact Us',
                'slug' => 'contact-us',
                'content' => '<h2>Get in Touch</h2><p>We\'d love to hear from you! Please reach out to us with any questions about adopting a pet.</p>',
                'status' => 'published',
                'is_special' => true,
            ],
            [
                'title' => 'About Us',
                'slug' => 'about-us',
                'content' => '<h2>Our Mission</h2><p>We are dedicated to finding loving homes for pets in need. Learn more about our organization and the work we do.</p>',
                'status' => 'published',
                'is_special' => true,
            ],
            [
                'title' => 'Privacy Policy',
                'slug' => 'privacy-policy',
                'content' => '<h2>Privacy Policy</h2><p>Your privacy is important to us. This policy outlines how we collect, use, and protect your personal information.</p>',
                'status' => 'published',
                'is_special' => true,
            ],
            [
                'title' => 'Terms of Service',
                'slug' => 'terms-of-service',
                'content' => '<h2>Terms of Service</h2><p>By using our website, you agree to comply with these terms and conditions.</p>',
                'status' => 'published',
                'is_special' => true,
            ],
        ];

        foreach ($specialPages as $pageData) {
            Page::firstOrCreate(
                ['slug' => $pageData['slug']],
                $pageData
            );
        }
    }
}
