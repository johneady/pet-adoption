<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\Page;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $happy_text = '<h1>A Happy Tail!</h1><p>Lorem ipsum dolor sit amet. Est repudiandae quia <em>Est tempore eos dolorem galisum et molestias atque vel molestiae adipisci</em> eos consequatur aliquam aut quisquam atque. Quo omnis ipsa <strong>Ut eaque qui nihil quidem id galisum debitis sit iste doloribus</strong> aut nesciunt quidem et consectetur numquam. Qui animi rerum aut assumenda laborumad quam et dolorum eius. Aut adipisci excepturiSit molestias sit omnis tempore. </p><p>Et commodi laborumut dolores et accusantium cumque. Ut atque totam et molestias commodi <em>Est quibusdam et dolorum totam sit deserunt totam qui expedita autem</em>. Et quia eiusAut alias aut esse dolor ut deleniti expedita id minus ullam in doloremque aliquid id repellat temporibus? </p><ol><li>Aut sunt provident aut nesciunt iste. </li><li>Est consequatur rerum est placeat dolore qui ratione aliquid ea minima mollitia. </li><li>Ut iusto magnam est aspernatur voluptatum. </li><li>Est inventore dolorem sit officiis dolorem et fuga dicta aut nesciunt corporis. </li><li>Ut omnis sunt qui labore officia quo iusto architecto aut adipisci quibusdam. </li><li>In dolorem explicabo ut illum galisum et ratione commodi sed quidem eius. </li></ol><p>33 libero harumAut suscipit qui ducimus officiis non harum mollitia. Et nulla architecto sed eveniet consectetur <strong>Vel praesentium ut quas quasi non atque magni</strong>. Ut natus nihil cum expedita nequeaut nulla rem recusandae inventore! </p>';
        $contact_text = '<h2>Get in Touch</h2><p>We\'d love to hear from you! Please reach out to us with any questions about adopting a pet.</p>';
        $about_text = '<h2>Our Mission</h2><p>We are dedicated to finding loving homes for pets in need. Learn more about our organization and the work we do.</p>';
        $privacy_text = '<h2>Privacy Policy</h2><p>Your privacy is important to us. This policy outlines how we collect, use, and protect your personal information.</p>';
        $tos_text = '<h2>Terms of Service</h2><p>By using our website, you agree to comply with these terms and conditions.</p>';

        // Get the About menu and Success Stories submenu
        $aboutMenu = Menu::where('slug', 'about')->first();
        $successStoriesMenu = Menu::where('slug', 'about/success-stories')->first();

        $specialPages = [
            [
                'title' => 'Contact Us',
                'slug' => 'contact-us',
                'content' => $contact_text,
                'status' => 'published',
                'is_special' => true,
            ],
            [
                'title' => 'About Us',
                'slug' => 'about-us',
                'content' => $about_text,
                'status' => 'published',
                'is_special' => true,
                'menu_id' => $aboutMenu?->id,
            ],
            [
                'title' => 'Privacy Policy',
                'slug' => 'privacy-policy',
                'content' => $privacy_text,
                'status' => 'published',
                'is_special' => true,
            ],
            [
                'title' => 'Terms of Service',
                'slug' => 'terms-of-service',
                'content' => $tos_text,
                'status' => 'published',
                'is_special' => true,
            ],
            [
                'title' => 'A Happy Tail',
                'slug' => 'a-happy-tail',
                'content' => $happy_text,
                'status' => 'published',
                'is_special' => false,
                'menu_id' => $aboutMenu?->id,
                'submenu_id' => $successStoriesMenu?->id,
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
