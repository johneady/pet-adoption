<?php

namespace App\Filament\Widgets;

use App\Models\AdoptionApplication;
use App\Models\Interview;
use App\Models\Pet;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat as StatsOverviewStat;

class PetsStatsWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            StatsOverviewStat::make('Total Pets', Pet::count())
                ->description('All pets in the system')
                ->descriptionIcon('heroicon-m-heart')
                ->color('info'),

            StatsOverviewStat::make('Available Pets', Pet::where('status', 'available')->count())
                ->description('Ready for adoption')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            StatsOverviewStat::make('Pending Applications', AdoptionApplication::whereIn('status', ['submitted', 'under_review'])->count())
                ->description('Awaiting review')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            StatsOverviewStat::make('Upcoming Interviews', Interview::whereNull('completed_at')
                ->where('scheduled_at', '>=', now())
                ->count())
                ->description('Scheduled interviews')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('primary'),
        ];
    }
}
