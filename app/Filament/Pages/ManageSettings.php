<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
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
                                        ->description('Upload your logo and favicon')
                                        ->schema([
                                            FileUpload::make('site_logo')
                                                ->label('Site Logo')
                                                ->image()
                                                ->directory('branding'),
                                            FileUpload::make('site_favicon')
                                                ->label('Favicon')
                                                ->image()
                                                ->directory('branding')
                                                ->acceptedFileTypes(['image/x-icon', 'image/png']),
                                        ])
                                        ->columns(2),
                                    Section::make('Maintenance')
                                        ->schema([
                                            Toggle::make('maintenance_mode')
                                                ->label('Maintenance Mode')
                                                ->helperText('Enable this to put the site in maintenance mode'),
                                        ]),
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
                                            FileUpload::make('seo_image')
                                                ->label('Social Sharing Image')
                                                ->image()
                                                ->directory('seo')
                                                ->helperText('Recommended: 1200x630px'),
                                        ])
                                        ->columns(1),
                                    Section::make('Tracking & Analytics')
                                        ->schema([
                                            TextInput::make('google_analytics_id')
                                                ->label('Google Analytics ID')
                                                ->placeholder('G-XXXXXXXXXX')
                                                ->maxLength(255),
                                            TextInput::make('google_site_verification')
                                                ->label('Google Site Verification Code')
                                                ->maxLength(255),
                                        ])
                                        ->columns(2),
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
                                            Textarea::make('contact_address')
                                                ->label('Physical Address')
                                                ->rows(3)
                                                ->maxLength(500),
                                        ])
                                        ->columns(1),
                                ]),
                            Tabs\Tab::make('Email')
                                ->icon(Heroicon::OutlinedAtSymbol)
                                ->schema([
                                    Section::make('Email Configuration')
                                        ->description('Configure default sender information for system emails')
                                        ->schema([
                                            TextInput::make('mail_from_address')
                                                ->label('From Email Address')
                                                ->email()
                                                ->required()
                                                ->maxLength(255),
                                            TextInput::make('mail_from_name')
                                                ->label('From Name')
                                                ->required()
                                                ->maxLength(255),
                                            TextInput::make('mail_reply_to')
                                                ->label('Reply-To Email')
                                                ->email()
                                                ->maxLength(255),
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
                            Tabs\Tab::make('Application')
                                ->icon(Heroicon::OutlinedDocumentText)
                                ->schema([
                                    Section::make('Adoption Settings')
                                        ->description('Configure adoption application settings')
                                        ->schema([
                                            Toggle::make('adoption_fee_enabled')
                                                ->label('Enable Adoption Fees')
                                                ->helperText('Enable or disable adoption fees')
                                                ->live(),
                                            TextInput::make('default_adoption_fee')
                                                ->label('Default Adoption Fee')
                                                ->numeric()
                                                ->prefix('$')
                                                ->minValue(0)
                                                ->step(0.01)
                                                ->visible(fn (callable $get) => $get('adoption_fee_enabled')),
                                            Toggle::make('require_application_approval')
                                                ->label('Require Application Approval')
                                                ->helperText('Require admin approval for adoption applications'),
                                            TextInput::make('max_applications_per_user')
                                                ->label('Max Applications Per User')
                                                ->numeric()
                                                ->minValue(1)
                                                ->helperText('Maximum number of active applications per user'),
                                        ])
                                        ->columns(2),
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

        Notification::make()
            ->success()
            ->title('Settings Saved')
            ->body('Your settings have been saved successfully.')
            ->send();
    }
}
