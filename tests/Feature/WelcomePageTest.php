<?php

declare(strict_types=1);

use App\Models\Setting;

test('welcome page loads successfully', function () {
    $response = $this->get('/');

    $response->assertSuccessful();
    $response->assertSee('Find Your Perfect Companion');
});

test('welcome page uses default images when no settings exist', function () {
    // Ensure no image settings exist
    Setting::whereIn('key', ['header_image', 'middle_image', 'footer_image'])->delete();

    $response = $this->get('/');

    $response->assertSuccessful();
    $response->assertSee('images/default_header.jpg', false);
    $response->assertSee('images/default_middle.jpg', false);
    $response->assertSee('images/default_footer.jpg', false);
});

test('welcome page uses custom images from settings when they exist', function () {
    // Create custom image settings
    Setting::set('header_image', 'custom/header.jpg', 'string', 'appearance');
    Setting::set('middle_image', 'custom/middle.jpg', 'string', 'appearance');
    Setting::set('footer_image', 'custom/footer.jpg', 'string', 'appearance');

    $response = $this->get('/');

    $response->assertSuccessful();
    $response->assertSee('custom/header.jpg', false);
    $response->assertSee('custom/middle.jpg', false);
    $response->assertSee('custom/footer.jpg', false);
});

test('welcome page uses default images when settings are null', function () {
    // Create settings with null values
    Setting::set('header_image', '', 'string', 'appearance');
    Setting::set('middle_image', '', 'string', 'appearance');
    Setting::set('footer_image', '', 'string', 'appearance');

    $response = $this->get('/');

    $response->assertSuccessful();
    $response->assertSee('images/default_header.jpg', false);
    $response->assertSee('images/default_middle.jpg', false);
    $response->assertSee('images/default_footer.jpg', false);
});
