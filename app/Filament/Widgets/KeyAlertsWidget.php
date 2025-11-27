<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\AdoptionApplications\AdoptionApplicationResource;
use App\Filament\Resources\Interviews\InterviewResource;
use App\Filament\Resources\TicketPurchaseRequests\TicketPurchaseRequestResource;
use App\Models\AdoptionApplication;
use App\Models\Interview;
use App\Models\Setting;
use App\Models\TicketPurchaseRequest;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class KeyAlertsWidget extends StatsOverviewWidget
{
    protected ?string $heading = 'Key Alerts';

    protected function getStats(): array
    {
        $requiresInterviewCount = AdoptionApplication::query()
            ->where('status', 'submitted')
            ->count();

        $overdueInterviewsCount = Interview::query()
            ->whereNull('completed_at')
            ->where('scheduled_at', '<', now())
            ->count();

        $requiresFinalDecisionCount = AdoptionApplication::query()
            ->where('status', 'under_review')
            ->count();

        $stats = [
            Stat::make('Requires Interview', $requiresInterviewCount)
                ->description('Applications awaiting interview scheduling')
                ->descriptionIcon('heroicon-m-calendar')
                ->color($requiresInterviewCount > 0 ? 'danger' : 'gray')
                ->url(AdoptionApplicationResource::getUrl('index', ['tableFilters' => ['status' => ['values' => ['submitted']]]])),

            Stat::make('Overdue Interviews', $overdueInterviewsCount)
                ->description('Interviews past their scheduled time')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($overdueInterviewsCount > 0 ? 'danger' : 'gray')
                ->url(InterviewResource::getUrl('index')),

            Stat::make('Requires Final Decision', $requiresFinalDecisionCount)
                ->description('Applications needing approval or rejection')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color($requiresFinalDecisionCount > 0 ? 'danger' : 'gray')
                ->url(AdoptionApplicationResource::getUrl('index', ['tableFilters' => ['status' => ['values' => ['under_review']]]])),
        ];

        // Only show pending ticket requests if 50/50 draws are enabled
        if (Setting::get('enable_draws', false)) {
            $pendingTicketRequestsCount = TicketPurchaseRequest::query()
                ->where('status', 'pending')
                ->count();

            $stats[] = Stat::make('Pending Ticket Requests', $pendingTicketRequestsCount)
                ->description('Ticket purchases awaiting processing')
                ->descriptionIcon('heroicon-m-ticket')
                ->color($pendingTicketRequestsCount > 0 ? 'danger' : 'gray')
                ->url(TicketPurchaseRequestResource::getUrl('index'));
        }

        return $stats;
    }
}
