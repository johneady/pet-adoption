<?php

declare(strict_types=1);

use App\Models\Setting;

test('footer displays social media links when populated in settings', function () {
    Setting::set('social_facebook', 'https://facebook.com/petadoption', 'string', 'social');
    Setting::set('social_twitter', 'https://twitter.com/petadoption', 'string', 'social');
    Setting::set('social_instagram', 'https://instagram.com/petadoption', 'string', 'social');
    Setting::set('social_youtube', 'https://youtube.com/petadoption', 'string', 'social');
    Setting::set('social_linkedin', 'https://linkedin.com/company/petadoption', 'string', 'social');

    $response = $this->get('/');

    $response->assertSuccessful();
    $response->assertSee('https://facebook.com/petadoption', false);
    $response->assertSee('https://twitter.com/petadoption', false);
    $response->assertSee('https://instagram.com/petadoption', false);
    $response->assertSee('https://youtube.com/petadoption', false);
    $response->assertSee('https://linkedin.com/company/petadoption', false);
});

test('footer does not display social media section when no links are populated', function () {
    Setting::set('social_facebook', null, 'string', 'social');
    Setting::set('social_twitter', null, 'string', 'social');
    Setting::set('social_instagram', null, 'string', 'social');
    Setting::set('social_youtube', null, 'string', 'social');
    Setting::set('social_linkedin', null, 'string', 'social');

    $response = $this->get('/');

    $response->assertSuccessful();
    $response->assertDontSee('aria-label="Facebook"', false);
    $response->assertDontSee('aria-label="Twitter"', false);
    $response->assertDontSee('aria-label="Instagram"', false);
    $response->assertDontSee('aria-label="YouTube"', false);
    $response->assertDontSee('aria-label="LinkedIn"', false);
});

test('footer displays only populated social media links', function () {
    Setting::set('social_facebook', 'https://facebook.com/petadoption', 'string', 'social');
    Setting::set('social_twitter', null, 'string', 'social');
    Setting::set('social_instagram', 'https://instagram.com/petadoption', 'string', 'social');
    Setting::set('social_youtube', 'https://youtube.com/petadoption', 'string', 'social');
    Setting::set('social_linkedin', null, 'string', 'social');

    $response = $this->get('/');

    $response->assertSuccessful();
    $response->assertSee('https://facebook.com/petadoption', false);
    $response->assertSee('https://instagram.com/petadoption', false);
    $response->assertSee('https://youtube.com/petadoption', false);
    $response->assertSee('aria-label="Facebook"', false);
    $response->assertSee('aria-label="Instagram"', false);
    $response->assertSee('aria-label="YouTube"', false);
    $response->assertDontSee('aria-label="Twitter"', false);
    $response->assertDontSee('aria-label="LinkedIn"', false);
});
