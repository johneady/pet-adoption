<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\AdoptionApplications\AdoptionApplicationResource;
use App\Models\AdoptionApplication;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class LatestApplicationsWidget extends TableWidget
{
    protected static ?string $heading = 'Latest Applications';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                fn (): Builder => AdoptionApplication::query()
                    ->latest()
                    ->limit(5)
            )
            ->columns([
                TextColumn::make('user.name')
                    ->label('Applicant')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('pet.name')
                    ->label('Pet')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'submitted' => 'gray',
                        'interview_scheduled' => 'warning',
                        'under_review' => 'info',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        'archived' => 'gray',
                        default => 'gray',
                    }),

                TextColumn::make('created_at')
                    ->label('Applied')
                    ->timezone(auth()->user()->timezone)
                    ->since()
                    ->sortable(),
            ])
            ->recordActions([
                Action::make('view')
                    ->url(fn (AdoptionApplication $record): string => AdoptionApplicationResource::getUrl('edit', ['record' => $record])),
            ]);
    }
}
