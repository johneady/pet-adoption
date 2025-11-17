<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;
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
                        TextEntry::make('created_at')
                            ->label('Member Since')
                            ->formatStateUsing(fn ($record): string => $record?->created_at?->format('F j, Y') ?? 'N/A'),
                    ])
                    ->columns(2),

                Section::make('User Information')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->readOnly(fn (string $operation): bool => $operation === 'edit')
                            ->dehydrated(fn (string $operation): bool => $operation === 'create')
                            ->maxLength(255),
                        TextInput::make('email')
                            ->email()
                            ->required()
                            ->readOnly(fn (string $operation): bool => $operation === 'edit')
                            ->dehydrated(fn (string $operation): bool => $operation === 'create')
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
                        TextEntry::make('account_status')
                            ->label('Account Status')
                            ->formatStateUsing(function ($record): \Illuminate\Contracts\Support\Htmlable {
                                if (! $record) {
                                    return new \Illuminate\Support\HtmlString('');
                                }

                                $status = $record->email_verified_at ? 'Verified' : 'Unverified';
                                $color = $record->email_verified_at ? 'success' : 'warning';

                                return new \Illuminate\Support\HtmlString(
                                    '<x-filament::badge color="'.$color.'">'.$status.'</x-filament::badge>'
                                );
                            })
                            ->hidden(fn (string $operation): bool => $operation === 'create'),
                        Toggle::make('is_admin')
                            ->label('Administrator')
                            ->helperText('Administrators have access to the admin panel.')
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
                    ->columns(2),
            ]);
    }
}
