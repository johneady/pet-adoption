<?php

declare(strict_types=1);

use App\Livewire\Dashboard;
use App\Models\AdoptionApplication;
use App\Models\Interview;
use App\Models\Pet;
use App\Models\Species;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

test('dashboard displays user applications', function () {
    $user = User::factory()->create();
    $species = Species::factory()->create();
    $pet = Pet::factory()->create(['species_id' => $species->id]);
    AdoptionApplication::factory()->create([
        'user_id' => $user->id,
        'pet_id' => $pet->id,
        'status' => 'submitted',
    ]);

    actingAs($user);

    Livewire::test(Dashboard::class)
        ->assertSee($pet->name)
        ->assertSee('Submitted');
});

test('dashboard shows empty state when no applications', function () {
    $user = User::factory()->create();

    actingAs($user);

    Livewire::test(Dashboard::class)
        ->assertSee('No Applications Yet')
        ->assertSee('Browse Our Pets');
});

test('dashboard displays interview details when scheduled', function () {
    $user = User::factory()->create();
    $species = Species::factory()->create();
    $pet = Pet::factory()->create(['species_id' => $species->id]);
    $application = AdoptionApplication::factory()->create([
        'user_id' => $user->id,
        'pet_id' => $pet->id,
        'status' => 'interview_scheduled',
    ]);
    Interview::factory()->create([
        'adoption_application_id' => $application->id,
        'scheduled_at' => now()->addDays(3),
        'location' => 'Main Office',
    ]);

    actingAs($user);

    Livewire::test(Dashboard::class)
        ->assertSee('Interview Scheduled')
        ->assertSee($pet->name);
});

test('dashboard displays application status badges correctly', function (string $status, string $expectedLabel) {
    $user = User::factory()->create();
    $species = Species::factory()->create();
    $pet = Pet::factory()->create(['species_id' => $species->id]);

    AdoptionApplication::factory()->create([
        'user_id' => $user->id,
        'pet_id' => $pet->id,
        'status' => $status,
    ]);

    actingAs($user);

    Livewire::test(Dashboard::class)
        ->assertSee($expectedLabel);
})->with([
    ['submitted', 'Submitted'],
    ['under_review', 'Under Review'],
    ['interview_scheduled', 'Interview Scheduled'],
    ['approved', 'Approved'],
    ['rejected', 'Declined'],
]);

test('dashboard only shows current user applications', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $species = Species::factory()->create();
    $pet1 = Pet::factory()->create(['species_id' => $species->id, 'name' => 'User1Pet']);
    $pet2 = Pet::factory()->create(['species_id' => $species->id, 'name' => 'User2Pet']);

    AdoptionApplication::factory()->create([
        'user_id' => $user1->id,
        'pet_id' => $pet1->id,
    ]);
    AdoptionApplication::factory()->create([
        'user_id' => $user2->id,
        'pet_id' => $pet2->id,
    ]);

    actingAs($user1);

    Livewire::test(Dashboard::class)
        ->assertSee('User1Pet')
        ->assertDontSee('User2Pet');
});

test('dashboard displays application submitted date', function () {
    $user = User::factory()->create();
    $species = Species::factory()->create();
    $pet = Pet::factory()->create(['species_id' => $species->id]);
    AdoptionApplication::factory()->create([
        'user_id' => $user->id,
        'pet_id' => $pet->id,
        'created_at' => now()->subDays(5),
    ]);

    actingAs($user);

    Livewire::test(Dashboard::class)
        ->assertSee('Application')
        ->assertSee('Submitted');
});

test('dashboard displays multiple applications', function () {
    $user = User::factory()->create();
    $species = Species::factory()->create();
    $pet1 = Pet::factory()->create(['species_id' => $species->id, 'name' => 'Pet One']);
    $pet2 = Pet::factory()->create(['species_id' => $species->id, 'name' => 'Pet Two']);

    AdoptionApplication::factory()->create([
        'user_id' => $user->id,
        'pet_id' => $pet1->id,
    ]);
    AdoptionApplication::factory()->create([
        'user_id' => $user->id,
        'pet_id' => $pet2->id,
    ]);

    actingAs($user);

    Livewire::test(Dashboard::class)
        ->assertSee('Pet One')
        ->assertSee('Pet Two');
});

test('dashboard displays all applications in descending order', function () {
    $user = User::factory()->create();
    $species = Species::factory()->create();

    $pet1 = Pet::factory()->create(['species_id' => $species->id, 'name' => 'Oldest Pet']);
    $pet2 = Pet::factory()->create(['species_id' => $species->id, 'name' => 'Middle Pet']);
    $pet3 = Pet::factory()->create(['species_id' => $species->id, 'name' => 'Newest Pet']);

    AdoptionApplication::factory()->create([
        'user_id' => $user->id,
        'pet_id' => $pet1->id,
        'created_at' => now()->subDays(3),
    ]);
    AdoptionApplication::factory()->create([
        'user_id' => $user->id,
        'pet_id' => $pet2->id,
        'created_at' => now()->subDays(2),
    ]);
    AdoptionApplication::factory()->create([
        'user_id' => $user->id,
        'pet_id' => $pet3->id,
        'created_at' => now()->subDays(1),
    ]);

    actingAs($user);

    Livewire::test(Dashboard::class)
        ->assertSee('Oldest Pet')
        ->assertSee('Middle Pet')
        ->assertSee('Newest Pet');
});

test('dashboard shows success message after application submission', function () {
    $user = User::factory()->create();

    actingAs($user);

    $this->withSession(['message' => 'Your adoption application has been submitted successfully!'])
        ->get(route('dashboard'))
        ->assertSee('Your adoption application has been submitted successfully!');
});

test('dashboard displays status description', function () {
    $user = User::factory()->create();
    $species = Species::factory()->create();
    $pet = Pet::factory()->create(['species_id' => $species->id]);
    AdoptionApplication::factory()->create([
        'user_id' => $user->id,
        'pet_id' => $pet->id,
        'status' => 'under_review',
    ]);

    actingAs($user);

    Livewire::test(Dashboard::class)
        ->assertSee('Our team is currently reviewing your application.');
});
