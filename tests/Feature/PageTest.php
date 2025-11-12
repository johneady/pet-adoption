<?php

use App\Models\Menu;
use App\Models\Page;
use App\Models\User;

test('users can view published pages', function () {
    $page = Page::factory()->create([
        'status' => 'published',
        'requires_auth' => false,
    ]);

    $response = $this->get(route('page.show', $page->slug));

    $response->assertSuccessful();
    $response->assertSee($page->title);
    $response->assertSee($page->content, false);
});

test('users cannot view draft pages', function () {
    $page = Page::factory()->create([
        'status' => 'draft',
    ]);

    $response = $this->get(route('page.show', $page->slug));

    $response->assertNotFound();
});

test('guests cannot view auth-required pages', function () {
    $page = Page::factory()->create([
        'status' => 'published',
        'requires_auth' => true,
    ]);

    $response = $this->get(route('page.show', $page->slug));

    $response->assertNotFound();
});

test('authenticated users can view auth-required pages', function () {
    $user = User::factory()->create();
    $page = Page::factory()->create([
        'status' => 'published',
        'requires_auth' => true,
    ]);

    $response = $this->actingAs($user)->get(route('page.show', $page->slug));

    $response->assertSuccessful();
    $response->assertSee($page->title);
});

test('special pages cannot be deleted', function () {
    $page = Page::factory()->create([
        'is_special' => true,
    ]);

    expect(fn () => $page->delete())
        ->toThrow(Exception::class, 'Special pages cannot be deleted.');
});

test('regular pages can be deleted', function () {
    $page = Page::factory()->create([
        'is_special' => false,
    ]);

    $page->delete();

    expect($page->trashed())->toBeTrue();
});

test('page slug is auto-generated from title', function () {
    $page = Page::factory()->create([
        'title' => 'My Test Page',
        'slug' => '',
    ]);

    expect($page->slug)->toBe('my-test-page');
});

test('pages can be assigned to menus', function () {
    $menu = Menu::factory()->create();
    $page = Page::factory()->create([
        'menu_id' => $menu->id,
    ]);

    expect($page->menu->id)->toBe($menu->id);
});

test('pages can be assigned to submenus', function () {
    $parentMenu = Menu::factory()->create();
    $submenu = Menu::factory()->create([
        'parent_id' => $parentMenu->id,
    ]);
    $page = Page::factory()->create([
        'menu_id' => $parentMenu->id,
        'submenu_id' => $submenu->id,
    ]);

    expect($page->submenu->id)->toBe($submenu->id);
});

test('page visible scope filters by published status and auth', function () {
    Page::factory()->create(['status' => 'draft']);
    Page::factory()->create(['status' => 'published', 'requires_auth' => false]);
    Page::factory()->create(['status' => 'published', 'requires_auth' => true]);

    $visiblePages = Page::visible()->get();

    expect($visiblePages)->toHaveCount(1);
});

test('page visible scope shows auth-required pages when user is authenticated', function () {
    $user = User::factory()->create();

    Page::factory()->create(['status' => 'draft']);
    Page::factory()->create(['status' => 'published', 'requires_auth' => false]);
    Page::factory()->create(['status' => 'published', 'requires_auth' => true]);

    $this->actingAs($user);

    $visiblePages = Page::visible()->get();

    expect($visiblePages)->toHaveCount(2);
});
