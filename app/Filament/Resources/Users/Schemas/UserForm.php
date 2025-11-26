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
                            ->maxSize(8192)
                            ->acceptedFileTypes(['image/jpeg', 'image/jpg', 'image/png', 'image/webp'])
                            ->imageResizeMode('cover')
                            ->imageResizeTargetWidth('150')
                            ->imageResizeTargetHeight('150')
                            ->saveUploadedFileUsing(function (TemporaryUploadedFile $file, $livewire): string {
                                // Get the old profile picture from the record
                                $oldProfilePicture = $livewire->getRecord()?->profile_picture;

                                // Delete old profile picture if it exists
                                if ($oldProfilePicture) {
                                    Storage::disk('public')->delete($oldProfilePicture);
                                }

                                // Generate unique filename
                                $filename = 'profile-pictures/'.uniqid().'.'.$file->getClientOriginalExtension();

                                // Resize and crop image to 150x150
                                $resizedImage = self::resizeAndCropImage($file->getRealPath(), 150, 150);

                                // Store compressed image
                                Storage::disk('public')->put($filename, $resizedImage);

                                return $filename;
                            })
                            ->deleteUploadedFileUsing(function (?string $file): void {
                                if ($file) {
                                    Storage::disk('public')->delete($file);
                                }
                            }),
                        Placeholder::make('created_at')
                            ->label('User Since')
                            ->content(fn ($record): string => $record?->created_at?->setTimezone(auth()->user()->timezone)->format('F j, Y') ?? 'N/A'),
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
                                $expiresAt = $membership->expires_at->setTimezone(auth()->user()->timezone)->format('M j, Y');

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
                        Toggle::make('banned')
                            ->label('Account Locked')
                            ->helperText(function ($livewire): string {
                                // Warn users they cannot ban themselves
                                if ($livewire->getRecord()?->id === auth()->id()) {
                                    return 'You cannot lock your own account.';
                                }

                                return 'Locked accounts cannot log in to the application.';
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
                        Toggle::make('receive_draw_result_alerts')
                            ->label('Receive Draw Result Alerts')
                            ->helperText('Receive notifications when draw results are announced.')
                            ->default(false),
                        Toggle::make('receive_ticket_purchase_alerts')
                            ->label('Receive Ticket Purchase Alerts')
                            ->helperText('Receive notifications when users submit ticket purchase requests.')
                            ->default(false),
                    ])
                    ->columns(2)
                    ->visible(fn ($record): bool => $record?->is_admin ?? false),
            ]);
    }

    /**
     * Resize and crop image to cover the specified dimensions using native GD.
     */
    protected static function resizeAndCropImage(string $sourcePath, int $targetWidth, int $targetHeight): string
    {
        // Get image info
        $imageInfo = getimagesize($sourcePath);
        $sourceWidth = $imageInfo[0];
        $sourceHeight = $imageInfo[1];
        $mimeType = $imageInfo['mime'];

        // Create source image from file
        $sourceImage = match ($mimeType) {
            'image/jpeg', 'image/jpg' => imagecreatefromjpeg($sourcePath),
            'image/png' => imagecreatefrompng($sourcePath),
            'image/webp' => imagecreatefromwebp($sourcePath),
            default => throw new \Exception('Unsupported image type'),
        };

        // Calculate dimensions for cover (crop to fill)
        $sourceAspect = $sourceWidth / $sourceHeight;
        $targetAspect = $targetWidth / $targetHeight;

        if ($sourceAspect > $targetAspect) {
            // Source is wider, crop width
            $newHeight = $sourceHeight;
            $newWidth = (int) ($sourceHeight * $targetAspect);
            $cropX = (int) (($sourceWidth - $newWidth) / 2);
            $cropY = 0;
        } else {
            // Source is taller, crop height
            $newWidth = $sourceWidth;
            $newHeight = (int) ($sourceWidth / $targetAspect);
            $cropX = 0;
            $cropY = (int) (($sourceHeight - $newHeight) / 2);
        }

        // Create destination image
        $destinationImage = imagecreatetruecolor($targetWidth, $targetHeight);

        // Preserve transparency for PNG and WebP
        if ($mimeType === 'image/png' || $mimeType === 'image/webp') {
            imagealphablending($destinationImage, false);
            imagesavealpha($destinationImage, true);
        }

        // Resample (crop and resize)
        imagecopyresampled(
            $destinationImage,
            $sourceImage,
            0, 0,
            $cropX, $cropY,
            $targetWidth, $targetHeight,
            $newWidth, $newHeight
        );

        // Output to string
        ob_start();
        match ($mimeType) {
            'image/jpeg', 'image/jpg' => imagejpeg($destinationImage, null, 90),
            'image/png' => imagepng($destinationImage, null, 9),
            'image/webp' => imagewebp($destinationImage, null, 90),
        };
        $imageData = ob_get_clean();

        // Clean up
        imagedestroy($sourceImage);
        imagedestroy($destinationImage);

        return $imageData;
    }
}
