<?php

namespace App\Filament\Resources\Users\Schemas;

use DateTimeZone;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Profile Picture')
                    ->schema([
                        FileUpload::make('profile_picture')
                            ->label('Profile Picture')
                            ->image()
                            ->avatar()
                            ->imageEditor()
                            ->circleCropper()
                            ->disk('public')
                            ->directory('profile-pictures')
                            ->maxSize(2048)
                            ->saveUploadedFileUsing(function (TemporaryUploadedFile $file, callable $set, callable $get, $livewire): string {
                                // Get the old profile picture from the record
                                $oldProfilePicture = $livewire->getRecord()?->profile_picture;

                                // Delete old profile picture if it exists
                                if ($oldProfilePicture) {
                                    Storage::disk('public')->delete($oldProfilePicture);
                                }

                                // Generate unique filename
                                $filename = 'profile-pictures/'.uniqid().'.'.$file->getClientOriginalExtension();

                                // Compress and resize image to 150x150
                                $image = Image::read($file->getRealPath());
                                $image->cover(150, 150);

                                // Store compressed image
                                Storage::disk('public')->put($filename, (string) $image->encode());

                                return $filename;
                            })
                            ->deleteUploadedFileUsing(function (?string $file): void {
                                if ($file) {
                                    Storage::disk('public')->delete($file);
                                }
                            }),
                        Placeholder::make('created_at')
                            ->label('User Since')
                            ->content(fn ($record): string => $record?->created_at?->format('F j, Y') ?? 'N/A'),
                    ])
                    ->columns(2),

                Section::make('Membership')
                    ->schema([
                        Placeholder::make('membership_status')
                            ->hiddenLabel()
                            ->content(function ($record): \Illuminate\Contracts\Support\Htmlable {
                                if (! $record) {
                                    return new \Illuminate\Support\HtmlString('');
                                }

                                $membership = $record->currentMembership;

                                if (! $membership || ! $membership->isActive()) {
                                    return new \Illuminate\Support\HtmlString(
                                        '<span class="text-gray-500 dark:text-gray-400">No active membership</span>'
                                    );
                                }

                                $plan = $membership->plan;
                                $daysRemaining = $membership->daysRemaining();
                                $expiresAt = $membership->expires_at->format('M j, Y');

                                return new \Illuminate\Support\HtmlString(
                                    '<div class="space-y-1">'.
                                    '<div><span style="background-color: '.$plan->badge_color.'; padding: 0.25rem 0.5rem; border-radius: 0.375rem; font-size: 0.75rem; font-weight: 500; color: white;">'.$plan->name.'</span></div>'.
                                    '<div class="text-sm text-gray-600 dark:text-gray-400">Expires '.$expiresAt.' ('.$daysRemaining.' days remaining)</div>'.
                                    '</div>'
                                );
                            }),
                    ])
                    ->hidden(fn (string $operation): bool => $operation === 'create'),

                Section::make('User Information')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->readOnly(function (string $operation, $livewire): bool {
                                if ($operation === 'create') {
                                    return false;
                                }

                                // Allow editing if the user is editing their own profile
                                return $livewire->getRecord()?->id !== auth()->id();
                            })
                            ->dehydrated()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->email()
                            ->required()
                            ->readOnly(function (string $operation, $livewire): bool {
                                if ($operation === 'create') {
                                    return false;
                                }

                                // Allow editing if the user is editing their own profile
                                return $livewire->getRecord()?->id !== auth()->id();
                            })
                            ->dehydrated()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        TextInput::make('password')
                            ->password()
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->dehydrated(fn (?string $state): bool => filled($state))
                            ->minLength(8)
                            ->maxLength(255)
                            ->autocomplete('new-password')
                            ->revealable()
                            ->hidden(fn (string $operation): bool => $operation === 'edit'),
                        Placeholder::make('account_status')
                            ->label('Account Status')
                            ->content(function ($record): \Illuminate\Contracts\Support\Htmlable {
                                if (! $record) {
                                    return new \Illuminate\Support\HtmlString('');
                                }

                                $status = $record->email_verified_at ? 'Verified' : 'Unverified';
                                $colors = $record->email_verified_at
                                    ? 'background-color: rgb(34 197 94); color: white;'
                                    : 'background-color: rgb(234 179 8); color: white;';

                                return new \Illuminate\Support\HtmlString(
                                    '<span style="'.$colors.' padding: 0.25rem 0.5rem; border-radius: 0.375rem; font-size: 0.75rem; font-weight: 500;">'.$status.'</span>'
                                );
                            })
                            ->hidden(fn (string $operation): bool => $operation === 'create'),
                        Toggle::make('is_admin')
                            ->label('Administrator')
                            ->helperText(function ($livewire): string {
                                // Warn users they cannot change their own admin status
                                if ($livewire->getRecord()?->id === auth()->id()) {
                                    return 'You cannot change your own administrator status to prevent lockout.';
                                }

                                return 'Administrators have access to the admin panel.';
                            })
                            ->disabled(fn ($livewire): bool => $livewire->getRecord()?->id === auth()->id())
                            ->dehydrated(fn ($livewire): bool => $livewire->getRecord()?->id !== auth()->id())
                            ->default(false),
                    ])
                    ->columns(2),

                Section::make('Contact Information')
                    ->schema([
                        TextInput::make('phone')
                            ->label('Phone Number')
                            ->tel()
                            ->maxLength(20),
                        TextInput::make('address')
                            ->label('Address')
                            ->columnSpanFull(),
                        Select::make('timezone')
                            ->label('Timezone')
                            ->options(fn () => collect(DateTimeZone::listIdentifiers())
                                ->mapWithKeys(fn ($tz) => [$tz => $tz])
                                ->toArray())
                            ->searchable()
                            ->default('America/Toronto'),
                    ])
                    ->columns(2),

                Section::make('Notification Preferences')
                    ->schema([
                        Toggle::make('receive_new_user_alerts')
                            ->label('Receive New User Alerts')
                            ->helperText('Receive notifications when new users register.')
                            ->default(false),
                        Toggle::make('receive_new_adoption_alerts')
                            ->label('Receive New Adoption Alerts')
                            ->helperText('Receive notifications when new adoption applications are submitted.')
                            ->default(false),
                    ])
                    ->columns(2)
                    ->visible(fn (): bool => auth()->user()?->is_admin ?? false),
            ]);
    }
}
