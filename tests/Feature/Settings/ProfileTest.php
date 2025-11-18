<?php

use App\Livewire\Applications\Create;
use App\Livewire\Settings\Profile;
use App\Models\Pet;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

test('user can update phone number', function () {
    $user = User::factory()->create(['phone' => null]);

    $this->actingAs($user);

    Livewire::test(Profile::class)
        ->set('phone', '555-1234')
        ->call('updateProfileInformation')
        ->assertHasNoErrors();

    expect($user->refresh()->phone)->toBe('555-1234');
});

test('user can update address', function () {
    $user = User::factory()->create(['address' => null]);

    $this->actingAs($user);

    Livewire::test(Profile::class)
        ->set('address', '123 Main St, City, State 12345')
        ->call('updateProfileInformation')
        ->assertHasNoErrors();

    expect($user->refresh()->address)->toBe('123 Main St, City, State 12345');
});

test('phone number cannot exceed 20 characters', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    Livewire::test(Profile::class)
        ->set('phone', str_repeat('1', 21))
        ->call('updateProfileInformation')
        ->assertHasErrors(['phone']);
});

test('address cannot exceed 500 characters', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    Livewire::test(Profile::class)
        ->set('address', str_repeat('a', 501))
        ->call('updateProfileInformation')
        ->assertHasErrors(['address']);
});

test('user can update timezone', function () {
    $user = User::factory()->create(['timezone' => 'America/Toronto']);

    $this->actingAs($user);

    Livewire::test(Profile::class)
        ->set('timezone', 'America/New_York')
        ->call('updateProfileInformation')
        ->assertHasNoErrors();

    expect($user->refresh()->timezone)->toBe('America/New_York');
});

test('timezone must be a valid timezone', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    Livewire::test(Profile::class)
        ->set('timezone', 'Invalid/Timezone')
        ->call('updateProfileInformation')
        ->assertHasErrors(['timezone']);
});

test('new users have default timezone of America/Toronto', function () {
    $user = User::factory()->create();

    expect($user->timezone)->toBe('America/Toronto');
});

test('user can upload profile picture', function () {
    Storage::fake('public');

    $user = User::factory()->create(['profile_picture' => null]);

    $this->actingAs($user);

    $file = UploadedFile::fake()->image('profile.jpg', 300, 300);

    Livewire::test(Profile::class)
        ->set('profilePicture', $file)
        ->call('updateProfileInformation')
        ->assertHasNoErrors();

    $user->refresh();

    expect($user->profile_picture)->not->toBeNull();
    Storage::disk('public')->assertExists($user->profile_picture);
});

test('profile picture is compressed to 150x150', function () {
    Storage::fake('public');

    $user = User::factory()->create(['profile_picture' => null]);

    $this->actingAs($user);

    $file = UploadedFile::fake()->image('profile.jpg', 1000, 1000);

    Livewire::test(Profile::class)
        ->set('profilePicture', $file)
        ->call('updateProfileInformation')
        ->assertHasNoErrors();

    $user->refresh();

    expect($user->profile_picture)->not->toBeNull();

    $imagePath = Storage::disk('public')->path($user->profile_picture);
    $imageSize = getimagesize($imagePath);

    expect($imageSize[0])->toBe(150);
    expect($imageSize[1])->toBe(150);
});

test('old profile picture is deleted when uploading new one', function () {
    Storage::fake('public');

    $oldFile = UploadedFile::fake()->image('old.jpg');
    $oldPath = $oldFile->store('profile-pictures', 'public');

    $user = User::factory()->create(['profile_picture' => $oldPath]);

    $this->actingAs($user);

    Storage::disk('public')->assertExists($oldPath);

    $newFile = UploadedFile::fake()->image('new.jpg');

    Livewire::test(Profile::class)
        ->set('profilePicture', $newFile)
        ->call('updateProfileInformation')
        ->assertHasNoErrors();

    Storage::disk('public')->assertMissing($oldPath);
    Storage::disk('public')->assertExists($user->refresh()->profile_picture);
});

test('user can remove profile picture', function () {
    Storage::fake('public');

    $file = UploadedFile::fake()->image('profile.jpg');
    $path = $file->store('profile-pictures', 'public');

    $user = User::factory()->create(['profile_picture' => $path]);

    $this->actingAs($user);

    Storage::disk('public')->assertExists($path);

    Livewire::test(Profile::class)
        ->set('removeProfilePicture', true)
        ->call('updateProfileInformation')
        ->assertHasNoErrors();

    $user->refresh();

    expect($user->profile_picture)->toBeNull();
    Storage::disk('public')->assertMissing($path);
});

test('profile picture must be an image', function () {
    Storage::fake('public');

    $user = User::factory()->create();

    $this->actingAs($user);

    $file = UploadedFile::fake()->create('document.pdf', 100);

    Livewire::test(Profile::class)
        ->set('profilePicture', $file)
        ->call('updateProfileInformation')
        ->assertHasErrors(['profilePicture']);
});

test('hasCompletedProfileForAdoption returns true when phone and address are set', function () {
    $user = User::factory()->create([
        'phone' => '555-1234',
        'address' => '123 Main St',
    ]);

    expect($user->hasCompletedProfileForAdoption())->toBeTrue();
});

test('hasCompletedProfileForAdoption returns false when phone is missing', function () {
    $user = User::factory()->create([
        'phone' => null,
        'address' => '123 Main St',
    ]);

    expect($user->hasCompletedProfileForAdoption())->toBeFalse();
});

test('hasCompletedProfileForAdoption returns false when address is missing', function () {
    $user = User::factory()->create([
        'phone' => '555-1234',
        'address' => null,
    ]);

    expect($user->hasCompletedProfileForAdoption())->toBeFalse();
});

test('hasCompletedProfileForAdoption returns false when both are missing', function () {
    $user = User::factory()->create([
        'phone' => null,
        'address' => null,
    ]);

    expect($user->hasCompletedProfileForAdoption())->toBeFalse();
});

test('profilePictureUrl returns url when profile picture exists', function () {
    Storage::fake('public');

    $file = UploadedFile::fake()->image('profile.jpg');
    $path = $file->store('profile-pictures', 'public');

    $user = User::factory()->create(['profile_picture' => $path]);

    expect($user->profilePictureUrl())->toBe(Storage::url($path));
});

test('profilePictureUrl returns empty string when no profile picture', function () {
    $user = User::factory()->create(['profile_picture' => null]);

    expect($user->profilePictureUrl())->toBe('');
});

test('user with incomplete profile is redirected when creating adoption application', function () {
    $user = User::factory()->incompleteProfile()->create();
    $pet = Pet::factory()->create(['status' => 'available']);

    $this->actingAs($user);

    Livewire::test(Create::class, ['petId' => $pet->id])
        ->assertRedirect(route('profile.edit'));

    expect(session('message'))->toContain('Please complete your profile');
});

test('user with complete profile can access adoption application form', function () {
    $user = User::factory()->create([
        'phone' => '555-1234',
        'address' => '123 Main St',
    ]);
    $pet = Pet::factory()->create(['status' => 'available']);

    $this->actingAs($user);

    $component = Livewire::test(Create::class, ['petId' => $pet->id]);

    expect($component->get('selectedPet')->id)->toBe($pet->id);
});
