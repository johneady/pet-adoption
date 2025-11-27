<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use App\Services\ThemeService;
use BackedEnum;
use DateTimeZone;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use UnitEnum;

/**
 * @property-read Schema $form
 */
class ManageSettings extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?string $navigationLabel = 'Settings';

    protected static UnitEnum|string|null $navigationGroup = 'Configuration';

    protected static ?int $navigationSort = 100;

    protected string $view = 'filament.pages.manage-settings';

    /**
     * @var array<string, mixed> | null
     */
    public ?array $data = [];

    public function mount(): void
    {
        $settings = Setting::all()->keyBy('key');

        $data = [];
        foreach ($settings as $key => $setting) {
            $data[$key] = $setting->getTypedValue();
        }

        $this->form->fill($data);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Form::make([
                    Tabs::make('Settings')
                        ->tabs([
                            Tabs\Tab::make('General')
                                ->icon(Heroicon::OutlinedInformationCircle)
                                ->schema([
                                    Section::make('Website Information')
                                        ->description('Basic information about your website')
                                        ->schema([
                                            TextInput::make('site_name')
                                                ->label('Site Name')
                                                ->required()
                                                ->maxLength(255),
                                            TextInput::make('site_tagline')
                                                ->label('Tagline')
                                                ->maxLength(255),
                                            Textarea::make('site_description')
                                                ->label('Description')
                                                ->rows(3)
                                                ->maxLength(500),
                                        ])
                                        ->columns(1),
                                    Section::make('Branding')
                                        ->description('Upload your logo and hero images')
                                        ->schema([
                                            FileUpload::make('site_logo')
                                                ->label('Site Logo')
                                                ->image()
                                                ->disk('public')
                                                ->directory('branding')
                                                ->imageResizeMode('force')
                                                ->imageResizeTargetWidth('150')
                                                ->imageResizeTargetHeight('150')
                                                ->imageCropAspectRatio('1:1')
                                                ->saveUploadedFileUsing(function (TemporaryUploadedFile $file): string {
                                                    // Generate unique filename
                                                    $filename = 'branding/'.uniqid().'.'.$file->getClientOriginalExtension();

                                                    // Resize and crop image to 150x150
                                                    $resizedImage = self::resizeAndCropImage($file->getRealPath(), 150, 150);

                                                    // Store compressed image
                                                    Storage::disk('public')->put($filename, $resizedImage);

                                                    return $filename;
                                                })
                                                ->deleteUploadedFileUsing(function (?string $file): void {
                                                    if ($file && Storage::disk('public')->exists($file)) {
                                                        Storage::disk('public')->delete($file);
                                                    }
                                                }),
                                            FileUpload::make('header_image')
                                                ->label('Header Image')
                                                ->helperText('Background image for the hero section (will be compressed to 800x600)')
                                                ->image()
                                                ->disk('public')
                                                ->directory('branding')
                                                ->imageResizeMode('force')
                                                ->imageResizeTargetWidth('800')
                                                ->imageResizeTargetHeight('600')
                                                ->saveUploadedFileUsing(function (TemporaryUploadedFile $file): string {
                                                    // Generate unique filename
                                                    $filename = 'branding/'.uniqid().'.'.$file->getClientOriginalExtension();

                                                    // Resize and crop image to 800x600
                                                    $resizedImage = self::resizeAndCropImage($file->getRealPath(), 800, 600);

                                                    // Store compressed image
                                                    Storage::disk('public')->put($filename, $resizedImage);

                                                    return $filename;
                                                })
                                                ->deleteUploadedFileUsing(function (?string $file): void {
                                                    if ($file && Storage::disk('public')->exists($file)) {
                                                        Storage::disk('public')->delete($file);
                                                    }
                                                }),
                                            FileUpload::make('middle_image')
                                                ->label('Middle Section Image')
                                                ->helperText('Background image for the middle section (will be compressed to 800x600)')
                                                ->image()
                                                ->disk('public')
                                                ->directory('branding')
                                                ->imageResizeMode('force')
                                                ->imageResizeTargetWidth('800')
                                                ->imageResizeTargetHeight('600')
                                                ->saveUploadedFileUsing(function (TemporaryUploadedFile $file): string {
                                                    // Generate unique filename
                                                    $filename = 'branding/'.uniqid().'.'.$file->getClientOriginalExtension();

                                                    // Resize and crop image to 800x600
                                                    $resizedImage = self::resizeAndCropImage($file->getRealPath(), 800, 600);

                                                    // Store compressed image
                                                    Storage::disk('public')->put($filename, $resizedImage);

                                                    return $filename;
                                                })
                                                ->deleteUploadedFileUsing(function (?string $file): void {
                                                    if ($file && Storage::disk('public')->exists($file)) {
                                                        Storage::disk('public')->delete($file);
                                                    }
                                                }),
                                            FileUpload::make('footer_image')
                                                ->label('Footer Image')
                                                ->helperText('Background image for the footer section (will be compressed to 800x600)')
                                                ->image()
                                                ->disk('public')
                                                ->directory('branding')
                                                ->imageResizeMode('force')
                                                ->imageResizeTargetWidth('800')
                                                ->imageResizeTargetHeight('600')
                                                ->saveUploadedFileUsing(function (TemporaryUploadedFile $file): string {
                                                    // Generate unique filename
                                                    $filename = 'branding/'.uniqid().'.'.$file->getClientOriginalExtension();

                                                    // Resize and crop image to 800x600
                                                    $resizedImage = self::resizeAndCropImage($file->getRealPath(), 800, 600);

                                                    // Store compressed image
                                                    Storage::disk('public')->put($filename, $resizedImage);

                                                    return $filename;
                                                })
                                                ->deleteUploadedFileUsing(function (?string $file): void {
                                                    if ($file && Storage::disk('public')->exists($file)) {
                                                        Storage::disk('public')->delete($file);
                                                    }
                                                }),
                                        ])
                                        ->columns(1),
                                    Section::make('Localization')
                                        ->description('Configure timezone settings')
                                        ->schema([
                                            Select::make('default_timezone')
                                                ->label('Default Timezone')
                                                ->options(fn () => collect(DateTimeZone::listIdentifiers())
                                                    ->mapWithKeys(fn ($tz) => [$tz => $tz])
                                                    ->toArray())
                                                ->searchable()
                                                ->required()
                                                ->default('America/Toronto')
                                                ->helperText('Default timezone for new users'),
                                        ])
                                        ->columns(1),
                                ]),
                            Tabs\Tab::make('SEO')
                                ->icon(Heroicon::OutlinedMagnifyingGlass)
                                ->schema([
                                    Section::make('Meta Tags')
                                        ->description('Default SEO meta tags for your website')
                                        ->schema([
                                            TextInput::make('seo_title')
                                                ->label('SEO Title')
                                                ->maxLength(60)
                                                ->helperText('Recommended: 50-60 characters'),
                                            Textarea::make('seo_description')
                                                ->label('Meta Description')
                                                ->rows(3)
                                                ->maxLength(160)
                                                ->helperText('Recommended: 150-160 characters'),
                                            TextInput::make('seo_keywords')
                                                ->label('Keywords')
                                                ->maxLength(255)
                                                ->helperText('Comma-separated keywords'),
                                        ])
                                        ->columns(1),
                                ]),
                            Tabs\Tab::make('Contact')
                                ->icon(Heroicon::OutlinedEnvelope)
                                ->schema([
                                    Section::make('Contact Information')
                                        ->schema([
                                            TextInput::make('contact_email')
                                                ->label('Email Address')
                                                ->email()
                                                ->required()
                                                ->maxLength(255),
                                            TextInput::make('contact_phone')
                                                ->label('Phone Number')
                                                ->tel()
                                                ->maxLength(255),
                                            TextInput::make('contact_address')
                                                ->label('Physical Address')
                                                ->maxLength(500),
                                        ])
                                        ->columns(1),
                                ]),
                            Tabs\Tab::make('Social Media')
                                ->icon(Heroicon::OutlinedShare)
                                ->schema([
                                    Section::make('Social Media Links')
                                        ->description('Add links to your social media profiles')
                                        ->schema([
                                            TextInput::make('social_facebook')
                                                ->label('Facebook')
                                                ->url()
                                                ->maxLength(255)
                                                ->placeholder('https://facebook.com/yourpage'),
                                            TextInput::make('social_twitter')
                                                ->label('Twitter / X')
                                                ->url()
                                                ->maxLength(255)
                                                ->placeholder('https://twitter.com/yourprofile'),
                                            TextInput::make('social_instagram')
                                                ->label('Instagram')
                                                ->url()
                                                ->maxLength(255)
                                                ->placeholder('https://instagram.com/yourprofile'),
                                            TextInput::make('social_youtube')
                                                ->label('YouTube')
                                                ->url()
                                                ->maxLength(255)
                                                ->placeholder('https://youtube.com/yourchannel'),
                                            TextInput::make('social_linkedin')
                                                ->label('LinkedIn')
                                                ->url()
                                                ->maxLength(255)
                                                ->placeholder('https://linkedin.com/company/yourcompany'),
                                        ])
                                        ->columns(1),
                                ]),
                            Tabs\Tab::make('Fundraising')
                                ->icon(Heroicon::CurrencyDollar)
                                ->schema([
                                    Section::make('Feature Toggles')
                                        ->description('Enable or disable fundraising features')
                                        ->schema([
                                            Toggle::make('enable_draws')
                                                ->label('Enable 50/50 Draws')
                                                ->helperText('Enable or disable 50/50 draw functionality'),
                                            Toggle::make('enable_memberships')
                                                ->label('Enable Memberships')
                                                ->helperText('Enable or disable membership donation functionality'),
                                        ])
                                        ->columns(2),
                                    Section::make('Draw Settings')
                                        ->description('Configure settings for 50/50 draws')
                                        ->schema([
                                            Textarea::make('draw_payment_info_text')
                                                ->label('Payment Information Message')
                                                ->rows(3)
                                                ->maxLength(500)
                                                ->helperText('Message displayed to users when purchasing draw tickets')
                                                ->required(),
                                        ])
                                        ->columns(1),
                                ]),
                            Tabs\Tab::make('Theme')
                                ->icon(Heroicon::OutlinedPaintBrush)
                                ->schema([
                                    Section::make('Color Theme')
                                        ->description('Customize the color scheme for your frontend pages')
                                        ->schema([
                                            Select::make('theme_preset')
                                                ->label('Color Theme Preset')
                                                ->options(fn () => app(ThemeService::class)->getPresetOptions())
                                                ->required()
                                                ->default('ocean-blue')
                                                ->helperText('Select a color theme for your website. Changes will apply to all frontend pages after saving.'),
                                        ])
                                        ->columns(1),
                                ]),
                        ])
                        ->columnSpanFull(),
                ])
                    ->livewireSubmitHandler('save')
                    ->footer([
                        Actions::make([
                            Action::make('save')
                                ->label('Save Settings')
                                ->icon(Heroicon::OutlinedCheck)
                                ->submit('save')
                                ->keyBindings(['mod+s']),
                        ]),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            $setting = Setting::where('key', $key)->first();

            if ($setting) {
                $setting->setTypedValue($value);
                $setting->save();
            }
        }

        Setting::clearCache();

        // Clear theme cache if theme settings were changed
        if (isset($data['theme_preset']) || isset($data['theme_primary_color']) || isset($data['theme_secondary_color'])) {
            app(ThemeService::class)->clearCache();
        }

        Notification::make()
            ->success()
            ->title('Settings Saved')
            ->body('Your settings have been saved successfully.')
            ->send();
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
