<?php

namespace App\Providers\Filament;

use App\Filament\Widgets\ApplicationsChart;
use App\Filament\Widgets\LatestApplicationsWidget;
use App\Filament\Widgets\PetsStatsWidget;
use App\Filament\Widgets\RecentUsersWidget;
use App\Models\Setting;
use Filament\Actions\Action;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->brandName(fn () => Setting::get('site_name', 'Pet Adoption Platform'))
            ->spa()
            ->spaUrlExceptions([
                url('/'),
            ])
            ->login()
            ->topbar(false)
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->colors([
                'primary' => Color::Blue,
            ])
            ->navigationItems([
                NavigationItem::make('Profile')
                    ->url('/settings')
                    ->icon('heroicon-o-user-circle')
                    ->sort(998),
                NavigationItem::make('View Website')
                    ->url('/')
                    ->icon('heroicon-o-globe-alt')
                    ->sort(999),
            ])
            ->userMenuItems([
                'logout' => fn (Action $action) => $action->hidden(),
                Action::make('viewWebsite')
                    ->label('View Website')
                    ->url(fn (): string => url('/'))
                    ->icon('heroicon-o-globe-alt'),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->widgets([
                PetsStatsWidget::class,
                ApplicationsChart::class,
                RecentUsersWidget::class,
                LatestApplicationsWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
