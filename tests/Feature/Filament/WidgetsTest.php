<?php

declare(strict_types=1);

use App\Filament\Widgets\ApplicationsChart;
use App\Filament\Widgets\LatestApplicationsWidget;
use App\Filament\Widgets\PetsStatsWidget;
use App\Filament\Widgets\RecentUsersWidget;
use App\Models\AdoptionApplication;
use App\Models\Interview;
use App\Models\Pet;
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

test('pets stats widget displays correct counts', function () {
    $species = \App\Models\Species::factory()->create();
    $breed = \App\Models\Breed::factory()->create(['species_id' => $species->id]);

    Pet::factory()->count(5)->create([
        'status' => 'available',
        'species_id' => $species->id,
        'breed_id' => $breed->id,
    ]);
    Pet::factory()->count(3)->create([
        'status' => 'adopted',
        'species_id' => $species->id,
        'breed_id' => $breed->id,
    ]);

    actingAs($this->admin);

    Livewire::test(PetsStatsWidget::class)
        ->assertSee('Total Pets')
        ->assertSee('8')
        ->assertSee('Available Pets')
        ->assertSee('5');
});

test('pets stats widget displays pending applications count', function () {
    $species = \App\Models\Species::factory()->create();
    $breed = \App\Models\Breed::factory()->create(['species_id' => $species->id]);
    $pet = Pet::factory()->create([
        'species_id' => $species->id,
        'breed_id' => $breed->id,
    ]);

    AdoptionApplication::factory()->count(3)->create([
        'status' => 'submitted',
        'pet_id' => $pet->id,
    ]);
    AdoptionApplication::factory()->count(1)->create([
        'status' => 'under_review',
        'pet_id' => $pet->id,
    ]);
    AdoptionApplication::factory()->count(2)->create([
        'status' => 'approved',
        'pet_id' => $pet->id,
    ]);

    actingAs($this->admin);

    Livewire::test(PetsStatsWidget::class)
        ->assertSee('Pending Applications')
        ->assertSee('4');
});

test('pets stats widget displays upcoming interviews count', function () {
    $application1 = AdoptionApplication::factory()->create();
    $application2 = AdoptionApplication::factory()->create();

    Interview::create([
        'adoption_application_id' => $application1->id,
        'scheduled_at' => now()->addDays(2),
        'location' => 'Office',
        'completed_at' => null,
    ]);

    Interview::create([
        'adoption_application_id' => $application2->id,
        'scheduled_at' => now()->addDays(3),
        'location' => 'Office',
        'completed_at' => now(),
    ]);

    actingAs($this->admin);

    Livewire::test(PetsStatsWidget::class)
        ->assertSee('Upcoming Interviews')
        ->assertSee('1');
});

test('applications chart widget renders', function () {
    AdoptionApplication::factory()->count(3)->create();

    actingAs($this->admin);

    Livewire::test(ApplicationsChart::class)
        ->assertSee('Adoption Applications');
});

test('latest applications widget displays recent applications', function () {
    $user = User::factory()->create(['name' => 'John Doe']);
    $pet = Pet::factory()->create(['name' => 'Max']);

    $application = AdoptionApplication::factory()->create([
        'user_id' => $user->id,
        'pet_id' => $pet->id,
        'status' => 'submitted',
    ]);

    actingAs($this->admin);

    Livewire::test(LatestApplicationsWidget::class)
        ->assertSee('Latest Applications')
        ->assertSee('John Doe')
        ->assertSee('Max')
        ->assertSee('submitted');
});

test('recent users widget displays recent users with time ago format', function () {
    User::factory()->create(['name' => 'Alice Smith', 'created_at' => now()->subMinutes(5)]);
    User::factory()->create(['name' => 'Bob Johnson', 'created_at' => now()->subHours(2)]);
    User::factory()->create(['name' => 'Charlie Brown', 'created_at' => now()->subDays(1)]);

    actingAs($this->admin);

    Livewire::test(RecentUsersWidget::class)
        ->assertSee('Recent Users')
        ->assertSee('Alice Smith')
        ->assertSee('Bob Johnson')
        ->assertSee('Charlie Brown')
        ->assertSee($this->admin->name);
});
