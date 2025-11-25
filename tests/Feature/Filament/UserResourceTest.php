<?php

declare(strict_types=1);

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
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

test('user resource table shows all users', function () {
    $users = User::factory()->count(5)->create();

    actingAs($this->admin);

    Livewire::test(ListUsers::class)
        ->assertCanSeeTableRecords($users)
        ->assertCountTableRecords(6); // 5 created + 1 admin
});

test('user resource table can search by name', function () {
    $user = User::factory()->create(['name' => 'John Doe']);
    User::factory()->create(['name' => 'Jane Smith']);

    actingAs($this->admin);

    Livewire::test(ListUsers::class)
        ->searchTable('John')
        ->assertCanSeeTableRecords([$user])
        ->assertCountTableRecords(1);
});

test('user resource table can search by email', function () {
    $user = User::factory()->create(['email' => 'john@example.com']);
    User::factory()->create(['email' => 'jane@example.com']);

    actingAs($this->admin);

    Livewire::test(ListUsers::class)
        ->searchTable('john@example.com')
        ->assertCanSeeTableRecords([$user])
        ->assertCountTableRecords(1);
});

test('user resource can create a new user', function () {
    actingAs($this->admin);

    Livewire::test(CreateUser::class)
        ->set('data.name', 'Test User Name')
        ->set('data.email', 'testcreate@example.com')
        ->set('data.password', 'password123')
        ->call('create')
        ->assertHasNoFormErrors();

    expect(User::where('email', 'testcreate@example.com')->exists())->toBeTrue();
});

test('user resource validates required fields on create', function () {
    actingAs($this->admin);

    Livewire::test(CreateUser::class)
        ->fillForm([
            'name' => '',
            'email' => '',
            'password' => '',
        ])
        ->call('create')
        ->assertHasFormErrors(['name', 'email', 'password']);
});

test('user resource validates unique email on create', function () {
    $existingUser = User::factory()->create(['email' => 'existing@example.com']);

    actingAs($this->admin);

    Livewire::test(CreateUser::class)
        ->fillForm([
            'name' => 'New User',
            'email' => 'existing@example.com',
            'password' => 'password123',
        ])
        ->call('create')
        ->assertHasFormErrors(['email']);
});

test('user resource can edit an existing user', function () {
    $user = User::factory()->create();

    actingAs($this->admin);

    Livewire::test(EditUser::class, ['record' => $user->id])
        ->assertOk()
        ->assertSchemaStateSet([
            'name' => $user->name,
            'email' => $user->email,
        ]);
});

test('user resource edit form shows all profile fields', function () {
    $user = User::factory()->admin()->create([
        'phone' => '555-1234',
        'address' => '123 Main St',
    ]);

    actingAs($this->admin);

    Livewire::test(EditUser::class, ['record' => $user->id])
        ->assertOk()
        ->assertSchemaStateSet([
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'address' => $user->address,
            'is_admin' => $user->is_admin,
            'receive_new_user_alerts' => $user->receive_new_user_alerts,
            'receive_new_adoption_alerts' => $user->receive_new_adoption_alerts,
            'receive_draw_result_alerts' => $user->receive_draw_result_alerts,
        ]);
});

test('user resource can update profile fields', function () {
    $user = User::factory()->admin()->incompleteProfile()->create([
        'receive_new_user_alerts' => false,
        'receive_new_adoption_alerts' => false,
        'receive_draw_result_alerts' => false,
    ]);

    actingAs($this->admin);

    Livewire::test(EditUser::class, ['record' => $user->id])
        ->assertOk()
        ->set('data.phone', '555-9876')
        ->set('data.address', '456 Oak Avenue, Apt 2B')
        ->set('data.receive_new_user_alerts', true)
        ->set('data.receive_new_adoption_alerts', true)
        ->set('data.receive_draw_result_alerts', true)
        ->assertHasNoFormErrors()
        ->call('save')
        ->assertHasNoFormErrors()
        ->assertNotified();

    $user->refresh();

    expect($user->phone)->toBe('555-9876')
        ->and($user->address)->toBe('456 Oak Avenue, Apt 2B')
        ->and($user->receive_new_user_alerts)->toBeTrue()
        ->and($user->receive_new_adoption_alerts)->toBeTrue()
        ->and($user->receive_draw_result_alerts)->toBeTrue();
});

test('user resource compresses uploaded profile picture to 150x150', function () {
    Storage::fake('public');

    $user = User::factory()->create([
        'phone' => '555-1234',
        'address' => '123 Test St',
    ]);

    actingAs($this->admin);

    // Create a test image file
    $testImage = Illuminate\Http\UploadedFile::fake()->image('profile.jpg', 500, 500);

    Livewire::test(EditUser::class, ['record' => $user->id])
        ->set('data.profile_picture', [$testImage])
        ->call('save')
        ->assertHasNoFormErrors()
        ->assertNotified();

    $user->refresh();

    // Verify the file was stored
    expect($user->profile_picture)->not->toBeNull()
        ->and(Storage::disk('public')->exists($user->profile_picture))->toBeTrue();

    // Verify the image is compressed to 150x150
    $storedImagePath = Storage::disk('public')->path($user->profile_picture);
    $imageSize = getimagesize($storedImagePath);

    expect($imageSize[0])->toBe(150)
        ->and($imageSize[1])->toBe(150);
});

test('user resource deletes old profile picture when uploading a new one', function () {
    Storage::fake('public');

    // Create user with existing profile picture
    $oldImagePath = 'profile-pictures/old-image.jpg';
    Storage::disk('public')->put($oldImagePath, 'old image content');

    $user = User::factory()->create([
        'profile_picture' => $oldImagePath,
        'phone' => '555-1234',
        'address' => '123 Test St',
    ]);

    actingAs($this->admin);

    // Upload a new image
    $newImage = Illuminate\Http\UploadedFile::fake()->image('new-profile.jpg', 300, 300);

    Livewire::test(EditUser::class, ['record' => $user->id])
        ->set('data.profile_picture', [$newImage])
        ->call('save')
        ->assertHasNoFormErrors()
        ->assertNotified();

    $user->refresh();

    // Verify old image was deleted
    expect(Storage::disk('public')->exists($oldImagePath))->toBeFalse()
        ->and($user->profile_picture)->not->toBe($oldImagePath)
        ->and(Storage::disk('public')->exists($user->profile_picture))->toBeTrue();
});

test('user resource deletes profile picture from storage when removed', function () {
    Storage::fake('public');

    // Create user with existing profile picture
    $imagePath = 'profile-pictures/test-image.jpg';
    Storage::disk('public')->put($imagePath, 'test image content');

    $user = User::factory()->create([
        'profile_picture' => $imagePath,
        'phone' => '555-1234',
        'address' => '123 Test St',
    ]);

    actingAs($this->admin);

    // Remove the profile picture by setting it to empty array (Filament way)
    Livewire::test(EditUser::class, ['record' => $user->id])
        ->set('data.profile_picture', [])
        ->call('save')
        ->assertHasNoFormErrors()
        ->assertNotified();

    $user->refresh();

    // Verify the image was deleted from storage and database
    expect(Storage::disk('public')->exists($imagePath))->toBeFalse()
        ->and($user->profile_picture)->toBeNull();
});

test('user resource accepts valid profile picture formats', function ($mimeType, $extension) {
    Storage::fake('public');

    $user = User::factory()->create([
        'phone' => '555-1234',
        'address' => '123 Test St',
    ]);

    actingAs($this->admin);

    $validImage = Illuminate\Http\UploadedFile::fake()->image("profile.{$extension}", 300, 300);

    Livewire::test(EditUser::class, ['record' => $user->id])
        ->set('data.profile_picture', [$validImage])
        ->call('save')
        ->assertHasNoFormErrors()
        ->assertNotified();

    $user->refresh();

    expect($user->profile_picture)->not->toBeNull()
        ->and(Storage::disk('public')->exists($user->profile_picture))->toBeTrue();
})->with([
    ['image/jpeg', 'jpg'],
    ['image/png', 'png'],
    ['image/webp', 'webp'],
]);
