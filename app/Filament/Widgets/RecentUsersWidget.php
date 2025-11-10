<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class RecentUsersWidget extends TableWidget
{
    protected static ?string $heading = 'Recent Users';

    // protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                fn (): Builder => User::query()
                    ->latest()
                    ->limit(4)
            )
            ->columns([
                TextColumn::make('name')
                    ->label('User Name')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Registered')
                    ->since()
                    ->sortable(),
            ])
            ->paginated(false);
    }
}
