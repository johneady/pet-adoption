<?php

declare(strict_types=1);

use App\Filament\Pages\ManageSettings;
use App\Models\Setting;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

beforeEach(function () {
    Filament::setCurrentPanel('admin');
    $this->admin = User::factory()->admin()->create();
    $this->seed(\Database\Seeders\SettingSeeder::class);
});

test('manage settings page can be accessed by admin', function () {
    actingAs($this->admin);

    Livewire::test(ManageSettings::class)
        ->assertSuccessful();
});

test('site logo can be uploaded and is resized to 150x150', function () {
    Storage::fake('public');

    actingAs($this->admin);

    // Create a test image larger than 150x150
    $file = UploadedFile::fake()->image('logo.png', 500, 500);

    Livewire::test(ManageSettings::class)
        ->fillForm([
            'site_logo' => $file,
        ])
        ->call('save')
        ->assertNotified();

    // Verify the setting was saved
    $setting = Setting::where('key', 'site_logo')->first();
    expect($setting)->not->toBeNull();

    // Get the uploaded file path
    $uploadedPath = $setting->value;

    // Verify file exists
    Storage::disk('public')->assertExists($uploadedPath);

    // Verify the image dimensions are 150x150
    $fullPath = Storage::disk('public')->path($uploadedPath);
    $imageSize = getimagesize($fullPath);

    expect($imageSize[0])->toBe(150)
        ->and($imageSize[1])->toBe(150);
});

test('settings can be saved successfully', function () {
    actingAs($this->admin);

    Livewire::test(ManageSettings::class)
        ->set('data.site_name', 'Test Site')
        ->set('data.site_tagline', 'A test tagline')
        ->set('data.contact_email', 'test@example.com')
        ->call('save')
        ->assertNotified();

    expect(Setting::where('key', 'site_name')->first()->fresh()->value)->toBe('Test Site')
        ->and(Setting::where('key', 'site_tagline')->first()->fresh()->value)->toBe('A test tagline')
        ->and(Setting::where('key', 'contact_email')->first()->fresh()->value)->toBe('test@example.com');
});

test('site logo is stored in public disk branding directory', function () {
    Storage::fake('public');

    actingAs($this->admin);

    $file = UploadedFile::fake()->image('logo.png', 500, 500);

    Livewire::test(ManageSettings::class)
        ->fillForm([
            'site_logo' => $file,
        ])
        ->call('save')
        ->assertNotified();

    $setting = Setting::where('key', 'site_logo')->first();
    $uploadedPath = $setting->value;

    // Verify it's stored in public disk
    expect(Storage::disk('public')->exists($uploadedPath))->toBeTrue()
        // Verify it's in the branding directory
        ->and(str_starts_with($uploadedPath, 'branding/'))->toBeTrue();
});
