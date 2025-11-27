<?php

declare(strict_types=1);

use App\Filament\Resources\Menus\Pages\ListMenus;
use App\Models\Menu;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

beforeEach(function () {
    Filament::setCurrentPanel('admin');
    $this->admin = User::factory()->admin()->create();
});

test('menu resource table shows all menus', function () {
    $menus = Menu::factory()->count(5)->create();

    actingAs($this->admin);

    Livewire::test(ListMenus::class)
        ->assertCanSeeTableRecords($menus)
        ->assertCountTableRecords(5);
});

test('menu resource table can search by name', function () {
    $menu = Menu::factory()->create(['name' => 'Home']);
    Menu::factory()->create(['name' => 'About']);

    actingAs($this->admin);

    Livewire::test(ListMenus::class)
        ->searchTable('Home')
        ->assertCanSeeTableRecords([$menu])
        ->assertCountTableRecords(1);
});

test('menu resource table can search by slug', function () {
    $menu = Menu::factory()->create(['slug' => 'contact-us']);
    Menu::factory()->create(['slug' => 'about-us']);

    actingAs($this->admin);

    Livewire::test(ListMenus::class)
        ->searchTable('contact')
        ->assertCanSeeTableRecords([$menu])
        ->assertCountTableRecords(1);
});

test('menu resource has create action', function () {
    actingAs($this->admin);

    Livewire::test(ListMenus::class)
        ->assertActionExists('create');
});

test('menu can be created directly via model', function () {
    actingAs($this->admin);

    $menu = Menu::create([
        'name' => 'Test Menu',
        'slug' => 'test-menu',
        'display_order' => 1,
        'is_visible' => true,
        'requires_auth' => false,
    ]);

    expect($menu->name)->toBe('Test Menu')
        ->and($menu->slug)->toBe('test-menu');
});

test('menu resource has edit action on table', function () {
    $menu = Menu::factory()->create();

    actingAs($this->admin);

    Livewire::test(ListMenus::class)
        ->assertTableActionExists('edit');
});

test('menu can be edited', function () {
    $menu = Menu::factory()->create([
        'name' => 'Original Name',
    ]);

    actingAs($this->admin);

    $menu->update(['name' => 'Updated Name']);

    expect($menu->fresh()->name)->toBe('Updated Name');
});

test('menu validates unique slug', function () {
    Menu::factory()->create(['slug' => 'existing-slug']);

    actingAs($this->admin);

    try {
        Menu::create([
            'name' => 'New Menu',
            'slug' => 'existing-slug',
            'display_order' => 1,
            'is_visible' => true,
            'requires_auth' => false,
        ]);
        $this->fail('Expected exception not thrown');
    } catch (\Illuminate\Database\QueryException $e) {
        expect($e->getMessage())->toContain('UNIQUE');
    }
});

test('menu resource can filter by visibility', function () {
    $visibleMenu = Menu::factory()->create(['is_visible' => true]);
    $hiddenMenu = Menu::factory()->create(['is_visible' => false]);

    actingAs($this->admin);

    Livewire::test(ListMenus::class)
        ->filterTable('is_visible', true)
        ->assertCanSeeTableRecords([$visibleMenu])
        ->assertCanNotSeeTableRecords([$hiddenMenu]);
});

test('menu resource can filter by authentication requirement', function () {
    $publicMenu = Menu::factory()->create(['requires_auth' => false]);
    $authMenu = Menu::factory()->create(['requires_auth' => true]);

    actingAs($this->admin);

    Livewire::test(ListMenus::class)
        ->filterTable('requires_auth', true)
        ->assertCanSeeTableRecords([$authMenu])
        ->assertCanNotSeeTableRecords([$publicMenu]);
});

test('menu resource can filter by level (top level vs submenus)', function () {
    $topLevelMenu = Menu::factory()->create(['parent_id' => null]);
    $parentMenu = Menu::factory()->create(['parent_id' => null]);
    $submenu = Menu::factory()->create(['parent_id' => $parentMenu->id]);

    actingAs($this->admin);

    Livewire::test(ListMenus::class)
        ->filterTable('parent_id', false)
        ->assertCanSeeTableRecords([$topLevelMenu, $parentMenu])
        ->assertCanNotSeeTableRecords([$submenu]);
});

test('menu can have parent menu', function () {
    $parent = Menu::factory()->create(['name' => 'Parent Menu']);
    $child = Menu::factory()->create([
        'name' => 'Child Menu',
        'parent_id' => $parent->id,
    ]);

    expect($child->parent->name)->toBe('Parent Menu')
        ->and($parent->children)->toHaveCount(1);
});

test('menu is_visible defaults to true', function () {
    $menu = Menu::factory()->create();

    expect($menu->is_visible)->toBeTrue();
});

test('menu requires_auth defaults to false', function () {
    $menu = Menu::factory()->create();

    expect($menu->requires_auth)->toBeFalse();
});

test('menu resource shows correct count of records', function () {
    Menu::factory()->count(7)->create();

    actingAs($this->admin);

    Livewire::test(ListMenus::class)
        ->assertCountTableRecords(7);
});

test('menu can update individual fields', function () {
    $menu = Menu::factory()->create([
        'name' => 'Original',
        'is_visible' => true,
    ]);

    actingAs($this->admin);

    $menu->update(['is_visible' => false]);

    expect($menu->fresh()->is_visible)->toBeFalse()
        ->and($menu->fresh()->name)->toBe('Original');
});
