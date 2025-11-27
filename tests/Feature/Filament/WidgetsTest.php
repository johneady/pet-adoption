<?php

declare(strict_types=1);

use App\Filament\Widgets\ApplicationsChart;
use App\Filament\Widgets\KeyAlertsWidget;
use App\Filament\Widgets\LatestApplicationsWidget;
use App\Filament\Widgets\PetsStatsWidget;
use App\Filament\Widgets\RecentUsersWidget;
use App\Models\AdoptionApplication;
use App\Models\Draw;
use App\Models\Interview;
use App\Models\Pet;
use App\Models\Setting;
use App\Models\TicketPurchaseRequest;
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

test('key alerts widget displays requires interview count', function () {
    $species = \App\Models\Species::factory()->create();
    $breed = \App\Models\Breed::factory()->create(['species_id' => $species->id]);
    $pet = Pet::factory()->create(['species_id' => $species->id, 'breed_id' => $breed->id]);

    AdoptionApplication::factory()->count(3)->create(['status' => 'submitted', 'pet_id' => $pet->id]);
    AdoptionApplication::factory()->count(2)->create(['status' => 'approved', 'pet_id' => $pet->id]);

    actingAs($this->admin);

    Livewire::test(KeyAlertsWidget::class)
        ->assertSee('Key Alerts')
        ->assertSee('Requires Interview')
        ->assertSee('3')
        ->assertSee('Applications awaiting interview scheduling');
});

test('key alerts widget displays overdue interviews count', function () {
    $application1 = AdoptionApplication::factory()->create();
    $application2 = AdoptionApplication::factory()->create();
    $application3 = AdoptionApplication::factory()->create();

    Interview::create([
        'adoption_application_id' => $application1->id,
        'scheduled_at' => now()->subDays(2),
        'location' => 'Office',
        'completed_at' => null,
    ]);

    Interview::create([
        'adoption_application_id' => $application2->id,
        'scheduled_at' => now()->subHours(5),
        'location' => 'Office',
        'completed_at' => null,
    ]);

    Interview::create([
        'adoption_application_id' => $application3->id,
        'scheduled_at' => now()->addDays(1),
        'location' => 'Office',
        'completed_at' => null,
    ]);

    actingAs($this->admin);

    Livewire::test(KeyAlertsWidget::class)
        ->assertSee('Overdue Interviews')
        ->assertSee('2')
        ->assertSee('Interviews past their scheduled time');
});

test('key alerts widget displays requires final decision count', function () {
    $species = \App\Models\Species::factory()->create();
    $breed = \App\Models\Breed::factory()->create(['species_id' => $species->id]);
    $pet = Pet::factory()->create(['species_id' => $species->id, 'breed_id' => $breed->id]);

    AdoptionApplication::factory()->count(4)->create(['status' => 'under_review', 'pet_id' => $pet->id]);
    AdoptionApplication::factory()->count(2)->create(['status' => 'submitted', 'pet_id' => $pet->id]);

    actingAs($this->admin);

    Livewire::test(KeyAlertsWidget::class)
        ->assertSee('Requires Final Decision')
        ->assertSee('4')
        ->assertSee('Applications needing approval or rejection');
});

test('key alerts widget displays pending ticket requests count when draws are enabled', function () {
    Setting::set('enable_draws', true, 'boolean', 'fundraising');

    $draw = Draw::factory()->create();
    $user = User::factory()->create();

    TicketPurchaseRequest::factory()->count(5)->create([
        'draw_id' => $draw->id,
        'user_id' => $user->id,
        'status' => 'pending',
    ]);
    TicketPurchaseRequest::factory()->count(3)->create([
        'draw_id' => $draw->id,
        'user_id' => $user->id,
        'status' => 'fulfilled',
    ]);

    actingAs($this->admin);

    Livewire::test(KeyAlertsWidget::class)
        ->assertSee('Pending Ticket Requests')
        ->assertSee('5')
        ->assertSee('Ticket purchases awaiting processing');
});

test('key alerts widget does not display pending ticket requests when draws are disabled', function () {
    Setting::set('enable_draws', false, 'boolean', 'fundraising');

    $draw = Draw::factory()->create();
    $user = User::factory()->create();

    TicketPurchaseRequest::factory()->count(5)->create([
        'draw_id' => $draw->id,
        'user_id' => $user->id,
        'status' => 'pending',
    ]);

    actingAs($this->admin);

    Livewire::test(KeyAlertsWidget::class)
        ->assertDontSee('Pending Ticket Requests')
        ->assertSee('Requires Interview')
        ->assertSee('Overdue Interviews')
        ->assertSee('Requires Final Decision');
});

test('key alerts widget displays zero counts when no alerts and draws enabled', function () {
    Setting::set('enable_draws', true, 'boolean', 'fundraising');

    actingAs($this->admin);

    Livewire::test(KeyAlertsWidget::class)
        ->assertSee('Key Alerts')
        ->assertSee('Requires Interview')
        ->assertSee('0')
        ->assertSee('Overdue Interviews')
        ->assertSee('Pending Ticket Requests')
        ->assertSee('Requires Final Decision');
});
