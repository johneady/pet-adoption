<?php

namespace App\Filament\Resources\Draws\Widgets;

use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Model;

class DrawTicketsWidget extends BaseWidget
{
    public ?Model $record = null;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Tickets';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                fn () => $this->record->tickets()->with('user')->getQuery()
            )
            ->columns([
                TextColumn::make('ticket_number')
                    ->label('Ticket #')
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Owner')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable(),
                IconColumn::make('is_winner')
                    ->boolean()
                    ->label('Winner'),
                TextColumn::make('created_at')
                    ->timezone(auth()->user()->timezone)
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('ticket_number', 'asc')
            ->paginated([10, 25, 50, 100]);
    }
}
