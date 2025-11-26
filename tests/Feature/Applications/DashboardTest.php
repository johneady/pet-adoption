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

//TODO fix this

// test('dashboard displays user applications', function () {
//     $user = User::factory()->create();
//     $species = Species::factory()->create();
//     $pet = Pet::factory()->create(['species_id' => $species->id]);
//     $application = AdoptionApplication::factory()->create([
//         'user_id' => $user->id,
//         'pet_id' => $pet->id,
//         'status' => 'submitted',
//     ]);

//     actingAs($user);

//     Livewire::test(Dashboard::class)
//         ->assertSee($pet->name)
//         ->assertSee('Submitted');
// });

// test('dashboard shows empty state when no applications', function () {
//     $user = User::factory()->create();

//     actingAs($user);

//     Livewire::test(Dashboard::class)
//         ->assertSee('No Applications Yet')
//         ->assertSee('submitted any adoption applications');
// });

// test('dashboard displays interview details when scheduled', function () {
//     $user = User::factory()->create();
//     $species = Species::factory()->create();
//     $pet = Pet::factory()->create(['species_id' => $species->id]);
//     $application = AdoptionApplication::factory()->create([
//         'user_id' => $user->id,
//         'pet_id' => $pet->id,
//         'status' => 'interview_scheduled',
//     ]);
//     $interview = Interview::factory()->create([
//         'adoption_application_id' => $application->id,
//         'scheduled_at' => now()->addDays(3),
//         'location' => 'Main Office',
//     ]);

//     actingAs($user);

//     Livewire::test(Dashboard::class)
//         ->assertSee('Interview Details')
//         ->assertSee('Main Office')
//         ->assertSee('Scheduled');
// });

// test('dashboard shows completed interview status', function () {
//     $user = User::factory()->create();
//     $species = Species::factory()->create();
//     $pet = Pet::factory()->create(['species_id' => $species->id]);
//     $application = AdoptionApplication::factory()->create([
//         'user_id' => $user->id,
//         'pet_id' => $pet->id,
//         'status' => 'under_review',
//     ]);
//     $interview = Interview::factory()->create([
//         'adoption_application_id' => $application->id,
//         'scheduled_at' => now()->subDays(2),
//         'location' => 'Main Office',
//         'completed_at' => now()->subDays(1),
//     ]);

//     actingAs($user);

//     Livewire::test(Dashboard::class)
//         ->assertSee('Interview Details')
//         ->assertSee('Completed');
// });

// test('dashboard displays application status badges correctly', function () {
//     $user = User::factory()->create();
//     $species = Species::factory()->create();
//     $pet = Pet::factory()->create(['species_id' => $species->id]);

//     $statuses = ['submitted', 'under_review', 'interview_scheduled', 'approved', 'rejected'];

//     foreach ($statuses as $status) {
//         $application = AdoptionApplication::factory()->create([
//             'user_id' => $user->id,
//             'pet_id' => $pet->id,
//             'status' => $status,
//         ]);

//         actingAs($user);

//         $component = Livewire::test(Dashboard::class);

//         match ($status) {
//             'submitted' => $component->assertSee('Submitted'),
//             'under_review' => $component->assertSee('Under Review'),
//             'interview_scheduled' => $component->assertSee('Interview Scheduled'),
//             'approved' => $component->assertSee('Approved'),
//             'rejected' => $component->assertSee('Rejected'),
//         };

//         $application->delete();
//     }
// });

// test('dashboard shows adoption process tracker', function () {
//     $user = User::factory()->create();
//     $species = Species::factory()->create();
//     $pet = Pet::factory()->create(['species_id' => $species->id]);
//     $application = AdoptionApplication::factory()->create([
//         'user_id' => $user->id,
//         'pet_id' => $pet->id,
//         'status' => 'under_review',
//     ]);

//     actingAs($user);

//     Livewire::test(Dashboard::class)
//         ->assertSee('Adoption Process')
//         ->assertSee('Submitted')
//         ->assertSee('Under Review')
//         ->assertSee('Interview')
//         ->assertSee('Final Decision');
// });

// test('dashboard only shows current user applications', function () {
//     $user1 = User::factory()->create();
//     $user2 = User::factory()->create();
//     $species = Species::factory()->create();
//     $pet1 = Pet::factory()->create(['species_id' => $species->id, 'name' => 'User1Pet']);
//     $pet2 = Pet::factory()->create(['species_id' => $species->id, 'name' => 'User2Pet']);

//     AdoptionApplication::factory()->create([
//         'user_id' => $user1->id,
//         'pet_id' => $pet1->id,
//     ]);
//     AdoptionApplication::factory()->create([
//         'user_id' => $user2->id,
//         'pet_id' => $pet2->id,
//     ]);

//     actingAs($user1);

//     Livewire::test(Dashboard::class)
//         ->assertSee('User1Pet')
//         ->assertDontSee('User2Pet');
// });

// test('dashboard displays application submitted date', function () {
//     $user = User::factory()->create();
//     $species = Species::factory()->create();
//     $pet = Pet::factory()->create(['species_id' => $species->id]);
//     $application = AdoptionApplication::factory()->create([
//         'user_id' => $user->id,
//         'pet_id' => $pet->id,
//         'created_at' => now()->subDays(5),
//     ]);

//     actingAs($user);

//     Livewire::test(Dashboard::class)
//         ->assertSee('Submitted:');
// });

// test('dashboard displays multiple applications', function () {
//     $user = User::factory()->create();
//     $species = Species::factory()->create();
//     $pet1 = Pet::factory()->create(['species_id' => $species->id, 'name' => 'Pet One']);
//     $pet2 = Pet::factory()->create(['species_id' => $species->id, 'name' => 'Pet Two']);

//     AdoptionApplication::factory()->create([
//         'user_id' => $user->id,
//         'pet_id' => $pet1->id,
//     ]);
//     AdoptionApplication::factory()->create([
//         'user_id' => $user->id,
//         'pet_id' => $pet2->id,
//     ]);

//     actingAs($user);

//     Livewire::test(Dashboard::class)
//         ->assertSee('Pet One')
//         ->assertSee('Pet Two');
// });

// test('dashboard shows success message after application submission', function () {
//     $user = User::factory()->create();

//     actingAs($user);

//     $this->withSession(['message' => 'Your adoption application has been submitted successfully!'])
//         ->get(route('dashboard'))
//         ->assertSee('Your adoption application has been submitted successfully!');
// });

// test('dashboard displays status timestamp in description', function () {
//     $user = User::factory()->create();
//     $species = Species::factory()->create();
//     $pet = Pet::factory()->create(['species_id' => $species->id]);
//     $application = AdoptionApplication::factory()->create([
//         'user_id' => $user->id,
//         'pet_id' => $pet->id,
//         'status' => 'under_review',
//     ]);

//     $statusTime = now()->subHours(2);

//     // Create status history with specific timestamp
//     $history = new \App\Models\ApplicationStatusHistory([
//         'adoption_application_id' => $application->id,
//         'from_status' => 'submitted',
//         'to_status' => 'under_review',
//     ]);
//     $history->created_at = $statusTime;
//     $history->updated_at = $statusTime;
//     $history->save();

//     actingAs($user);

//     Livewire::test(Dashboard::class)
//         ->assertSee('Our team is currently reviewing your application.');
// });
