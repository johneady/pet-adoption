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
        $privacy_text = '<h2>Privacy Policy</h2><h3>1. Introduction</h3><p>Your privacy is important to us. This policy outlines how we collect, use, and protect your information when you visit <strong>[Your Website Name/URL Here]</strong>.</p><h3>2. Information We Collect</h3><p>We only collect information necessary to provide and improve our services. This may include:</p><ul><li><p><strong>Personal Identification Information:</strong> If you contact us or use a service requiring registration (e.g., name, email address).</p></li><li><p><strong>Usage Data/Log Files:</strong> Information your browser sends when you visit our site (e.g., IP address, browser type, pages visited, time spent on pages).</p></li><li><p><strong>Cookies:</strong> Small data files stored on your device to help us improve your experience and analyze site usage.</p></li></ul><h3>3. How We Use Your Information</h3><p>We use the collected data for the following purposes:</p><ul><li><p>To <strong>operate and maintain</strong> our website.</p></li><li><p>To <strong>improve, personalize, and expand</strong> our website.</p></li><li><p>To <strong>understand and analyze</strong> how you use our website.</p></li><li><p>To <strong>communicate with you</strong>, either directly or through one of our partners, including for customer service and to provide you with updates and other information relating to the website.</p></li></ul><h3>4. Third-Party Sharing</h3><p>We do not sell, trade, or otherwise transfer your personally identifiable information to outside parties. This exclusion does not include website hosting partners and other parties who assist us in operating our website, conducting our business, or serving our users, so long as those parties agree to keep this information confidential. We may also release information when its release is appropriate to comply with the law, enforce our site policies, or protect ours or others&#039; rights, property, or safety.</p><h3>5. Your Rights</h3><p>Depending on your location, you may have the right to access, update, or delete the personal information we hold about you. Please contact us to exercise these rights.</p><h3>6. Updates</h3><p>We may update this Privacy Policy from time to time. We will notify you of any changes by posting the new Privacy Policy on this page.</p><h3>7. Contact Us</h3><p>If you have any questions about this Privacy Policy, please contact us at <strong>[Your Contact Email Address Here]</strong>.</p>';
        $tos_text = '<h2>Terms of Service (ToS)</h2><p>Welcome to <strong>[Your Website Name/URL Here]</strong>! These Terms of Service govern your use of the website. By accessing or using our site, you agree to be bound by these terms.</p><h3>1. Acceptance of Terms</h3><p>By using this website, you confirm that you are at least the age of majority in your state or province of residence, or that you are the age of majority in your state or province of residence and you have given us your consent to allow any of your minor dependents to use this site.</p><h3>2. Intellectual Property</h3><p>All content on this site, including text, graphics, logos, and images, is the property of <strong>[Your Company Name]</strong> or its content suppliers and is protected by copyright and intellectual property laws. You may not reproduce, duplicate, copy, sell, or exploit any portion of the content without express written permission from us.</p><h3>3. User Conduct</h3><p>You agree not to use the website for any unlawful purpose or in any way that might damage, disable, overburden, or impair the site. You must not introduce any viruses, Trojan horses, worms, or other malicious code.</p><h3>4. Disclaimer of Warranties</h3><p>The website is provided on an &quot;as is&quot; and &quot;as available&quot; basis. We make no representations or warranties of any kind, express or implied, as to the operation of the site or the information, content, or materials included on the site.</p><h3>5. Limitation of Liability</h3><p><strong>[Your Company Name]</strong> will not be liable for any damages of any kind arising from the use of this site, including, but not limited to direct, indirect, incidental, punitive, and consequential damages.</p><h3>6. Governing Law</h3><p>These Terms of Service shall be governed by and construed in accordance with the laws of <strong>[Your Jurisdiction/State/Province and Country, e.g., the Province of Ontario and the laws of Canada]</strong>, without regard to its conflict of law principles.</p><h3>7. Changes to Terms</h3><p>We reserve the right to update, change, or replace any part of these Terms of Service by posting updates and/or changes to our website. It is your responsibility to check this page periodically for changes.</p><h3>8. Contact Information</h3><p>Questions about the Terms of Service should be sent to us at <strong>[Your Contact Email Address Here]</strong>.</p>';

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
