<?php

namespace App\Filament\Widgets;

use App\Models\AdoptionApplication;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class ApplicationsChart extends ChartWidget
{
    protected ?string $heading = 'Adoption Applications';

    protected function getData(): array
    {
        $data = $this->getApplicationsPerMonth();

        return [
            'datasets' => [
                [
                    'label' => 'Applications',
                    'data' => $data['counts'],
                    'borderColor' => 'rgb(59, 130, 246)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                ],
            ],
            'labels' => $data['labels'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getApplicationsPerMonth(): array
    {
        $now = Carbon::now();
        $months = [];
        $labels = [];
        $counts = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = $now->copy()->subMonths($i);
            $months[] = $date->format('Y-m');
            $labels[] = $date->format('M Y');
        }

        $applications = AdoptionApplication::query()
            ->where('created_at', '>=', $now->copy()->subMonths(11)->startOfMonth())
            ->get()
            ->groupBy(fn ($application) => Carbon::parse($application->created_at)->format('Y-m'));

        foreach ($months as $month) {
            $counts[] = $applications->get($month)?->count() ?? 0;
        }

        return [
            'labels' => $labels,
            'counts' => $counts,
        ];
    }
}
