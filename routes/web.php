<?php

use App\Http\Controllers\PageController;
use App\Livewire\Applications\Create as ApplicationsCreate;
use App\Livewire\Blog\Index as BlogIndex;
use App\Livewire\Blog\Show as BlogShow;
use App\Livewire\Dashboard;
use App\Livewire\Pets\Index as PetsIndex;
use App\Livewire\Pets\Show as PetsShow;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Settings\TwoFactor;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/pets', PetsIndex::class)->name('pets.index');
Route::get('/pets/{slug}', PetsShow::class)->name('pets.show');

Route::get('/blog', BlogIndex::class)->name('blog.index');
Route::get('/blog/{slug}', BlogShow::class)->name('blog.show');

Route::get('/page/{slug}', [PageController::class, 'show'])->name('page.show');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', Dashboard::class)->name('dashboard');
    Route::get('applications/create/{petId}', ApplicationsCreate::class)->name('applications.create');
});

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('profile.edit');
    Route::get('settings/password', Password::class)->name('user-password.edit');
    Route::get('settings/appearance', Appearance::class)->name('appearance.edit');

    Route::get('settings/two-factor', TwoFactor::class)
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');
});
