<?php

use App\Filament\Resources\Draws\Pages\CreateDraw;
use App\Models\Draw;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->admin()->create();
    $this->actingAs($this->admin);
});

describe('Draw Overlap Validation', function () {
    it('prevents creating a draw that overlaps with an existing draw', function () {
        Draw::factory()->upcoming()->create([
            'starts_at' => now()->addDays(10),
            'ends_at' => now()->addDays(20),
            'name' => 'Existing Draw',
        ]);

        Livewire::test(CreateDraw::class)
            ->fillForm([
                'name' => 'New Draw',
                'description' => 'Test description',
                'starts_at' => now()->addDays(15),
                'ends_at' => now()->addDays(25),
                'ticket_price_tiers' => [
                    ['quantity' => 1, 'price' => 1.00],
                ],
            ])
            ->call('create')
            ->assertHasFormErrors(['starts_at', 'ends_at']);
    });

    it('prevents creating a draw that completely encompasses an existing draw', function () {
        Draw::factory()->upcoming()->create([
            'starts_at' => now()->addDays(15),
            'ends_at' => now()->addDays(20),
            'name' => 'Existing Draw',
        ]);

        Livewire::test(CreateDraw::class)
            ->fillForm([
                'name' => 'New Draw',
                'description' => 'Test description',
                'starts_at' => now()->addDays(10),
                'ends_at' => now()->addDays(25),
                'ticket_price_tiers' => [
                    ['quantity' => 1, 'price' => 1.00],
                ],
            ])
            ->call('create')
            ->assertHasFormErrors(['starts_at', 'ends_at']);
    });

    it('prevents creating a draw that is completely within an existing draw', function () {
        Draw::factory()->upcoming()->create([
            'starts_at' => now()->addDays(10),
            'ends_at' => now()->addDays(30),
            'name' => 'Existing Draw',
        ]);

        Livewire::test(CreateDraw::class)
            ->fillForm([
                'name' => 'New Draw',
                'description' => 'Test description',
                'starts_at' => now()->addDays(15),
                'ends_at' => now()->addDays(25),
                'ticket_price_tiers' => [
                    ['quantity' => 1, 'price' => 1.00],
                ],
            ])
            ->call('create')
            ->assertHasFormErrors(['starts_at', 'ends_at']);
    });

    it('provides a helpful error message when overlap is detected', function () {
        Draw::factory()->upcoming()->create([
            'starts_at' => now()->addDays(10),
            'ends_at' => now()->addDays(20),
            'name' => 'Existing Draw',
        ]);

        $component = Livewire::test(CreateDraw::class)
            ->fillForm([
                'name' => 'New Draw',
                'description' => 'Test description',
                'starts_at' => now()->addDays(15),
                'ends_at' => now()->addDays(25),
                'ticket_price_tiers' => [
                    ['quantity' => 1, 'price' => 1.00],
                ],
            ])
            ->call('create');

        $errors = $component->errors();
        $startsAtError = $errors->get('data.starts_at')[0] ?? '';

        expect($startsAtError)->toContain('Existing Draw');
    });
});
