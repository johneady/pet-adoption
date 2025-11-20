<?php

use App\Http\Controllers\PageController;
use App\Http\Controllers\Webhooks\PayPalIPNController;
use App\Livewire\Applications\Create as ApplicationsCreate;
use App\Livewire\Blog\Index as BlogIndex;
use App\Livewire\Blog\Show as BlogShow;
use App\Livewire\Dashboard;
use App\Livewire\Draws\Index as DrawsIndex;
use App\Livewire\Draws\PurchaseTickets;
use App\Livewire\Membership\Cancel;
use App\Livewire\Membership\Checkout;
use App\Livewire\Membership\Manage;
use App\Livewire\Membership\Plans;
use App\Livewire\Membership\Success;
use App\Livewire\Pets\Index as PetsIndex;
use App\Livewire\Pets\Show as PetsShow;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Notifications;
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

Route::get('/draws', DrawsIndex::class)->name('draws.index');

Route::get('/page/{slug}', [PageController::class, 'show'])->name('page.show');

Route::get('/membership', Plans::class)->name('membership.plans');

// PayPal IPN webhook route (CSRF exemption configured in bootstrap/app.php)
Route::post('/webhooks/paypal-ipn', [PayPalIPNController::class, 'handleIPN'])->name('webhooks.paypal-ipn');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', Dashboard::class)->name('dashboard');
    Route::get('applications/create/{petId}', ApplicationsCreate::class)->name('applications.create');

    Route::get('/draws/{draw}/purchase', PurchaseTickets::class)->name('draws.purchase');

    Route::get('/membership/checkout/{plan}', Checkout::class)->name('membership.checkout');
    Route::get('/membership/success', Success::class)->name('membership.success');
    Route::get('/membership/cancel', Cancel::class)->name('membership.cancel');
    Route::get('/membership/manage', Manage::class)->name('membership.manage');
});

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('profile.edit');
    Route::get('settings/password', Password::class)->name('user-password.edit');
    Route::get('settings/appearance', Appearance::class)->name('appearance.edit');
    Route::get('settings/notifications', Notifications::class)->name('notifications.edit');

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
