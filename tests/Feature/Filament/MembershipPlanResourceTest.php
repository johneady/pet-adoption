<?php

declare(strict_types=1);

use App\Filament\Resources\MembershipPlans\Pages\ListMembershipPlans;
use App\Models\MembershipPlan;
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

test('membership plan can be edited', function () {
    $plan = MembershipPlan::factory()->create([
        'name' => 'Original Name',
        'price' => 10.00,
    ]);

    actingAs($this->admin);

    $plan->update([
        'name' => 'Updated Name',
        'price' => 15.00,
    ]);

    expect($plan->fresh()->name)->toBe('Updated Name')
        ->and($plan->fresh()->price)->toBe('15.00');
});

test('membership plan can be created with all fillable fields', function () {
    actingAs($this->admin);

    $plan = MembershipPlan::create([
        'name' => 'Gold Plan',
        'slug' => 'gold-plan',
        'price' => 99.99,
        'description' => 'Premium membership with all features',
        'features' => ['Feature 1', 'Feature 2', 'Feature 3'],
        'badge_color' => '#FFD700',
        'display_order' => 1,
        'is_active' => true,
    ]);

    expect($plan->name)->toBe('Gold Plan')
        ->and($plan->slug)->toBe('gold-plan')
        ->and($plan->price)->toBe('99.99')
        ->and($plan->description)->toBe('Premium membership with all features')
        ->and($plan->features)->toBe(['Feature 1', 'Feature 2', 'Feature 3'])
        ->and($plan->badge_color)->toBe('#FFD700')
        ->and($plan->display_order)->toBe(1)
        ->and($plan->is_active)->toBeTrue();
});

test('membership plan resource has edit action on table', function () {
    $plan = MembershipPlan::factory()->create();

    actingAs($this->admin);

    Livewire::test(ListMembershipPlans::class)
        ->assertTableActionExists('edit');
});

test('membership plan can update individual fields', function () {
    $plan = MembershipPlan::factory()->create([
        'name' => 'Bronze',
        'badge_color' => '#CD7F32',
    ]);

    actingAs($this->admin);

    $plan->update(['badge_color' => '#FFD700']);

    expect($plan->fresh()->badge_color)->toBe('#FFD700')
        ->and($plan->fresh()->name)->toBe('Bronze');
});

test('membership plan features are properly cast to array', function () {
    $plan = MembershipPlan::factory()->create([
        'features' => ['Feature 1', 'Feature 2'],
    ]);

    expect($plan->features)->toBeArray()
        ->and($plan->features)->toBe(['Feature 1', 'Feature 2']);
});
