<?php

namespace App\Filament\Resources\Pets\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class PetForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic Information')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, callable $set, $get) => $set('slug', \Illuminate\Support\Str::slug($state).'-'.random_int(1000, 9999)))
                            ->disabled(fn ($record) => $record?->status === 'adopted'),
                        TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->disabled(fn ($record) => $record?->status === 'adopted'),
                        Select::make('species_id')
                            ->relationship('species', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(fn (callable $set) => $set('breed_id', null))
                            ->disabled(fn ($record) => $record?->status === 'adopted'),
                        Select::make('breed_id')
                            ->relationship(
                                'breed',
                                'name',
                                fn ($query, callable $get) => $query->where('species_id', $get('species_id'))
                            )
                            ->searchable()
                            ->preload()
                            ->disabled(fn ($record, callable $get) => ! $get('species_id') || $record?->status === 'adopted'),
                        TextInput::make('age')
                            ->numeric()
                            ->suffix('years')
                            ->minValue(0)
                            ->maxValue(30)
                            ->disabled(fn ($record) => $record?->status === 'adopted'),
                        Select::make('gender')
                            ->options([
                                'male' => 'Male',
                                'female' => 'Female',
                                'unknown' => 'Unknown',
                            ])
                            ->required()
                            ->default('unknown')
                            ->disabled(fn ($record) => $record?->status === 'adopted'),
                        Select::make('size')
                            ->options([
                                'small' => 'Small',
                                'medium' => 'Medium',
                                'large' => 'Large',
                                'extra_large' => 'Extra Large',
                            ])
                            ->disabled(fn ($record) => $record?->status === 'adopted'),
                        TextInput::make('color')
                            ->disabled(fn ($record) => $record?->status === 'adopted'),
                        Toggle::make('vaccination_status')
                            ->label('Vaccinated')
                            ->default(false)
                            ->disabled(fn ($record) => $record?->status === 'adopted'),
                        Toggle::make('special_needs')
                            ->label('Special Needs')
                            ->default(false)
                            ->disabled(fn ($record) => $record?->status === 'adopted'),
                        DatePicker::make('intake_date')
                            ->required()
                            ->native(false)
                            ->timezone(auth()->user()->timezone)
                            ->default(now())
                            ->maxDate(now())
                            ->disabled(fn ($record) => $record?->status === 'adopted'),
                        ToggleButtons::make('status')
                            ->options([
                                'available' => 'Available',
                                'pending' => 'Pending',
                                'adopted' => 'Adopted',
                                'coming_soon' => 'Coming Soon',
                            ])
                            ->icons([
                                'available' => Heroicon::OutlinedCheckCircle,
                                'pending' => Heroicon::OutlinedClock,
                                'adopted' => Heroicon::OutlinedHeart,
                                'coming_soon' => Heroicon::OutlinedXCircle,
                            ])
                            ->colors([
                                'available' => 'success',
                                'pending' => 'warning',
                                'adopted' => 'gray',
                                'coming_soon' => 'danger',
                            ])
                            ->inline()
                            ->required()
                            ->default('available')
                            ->columnSpanFull(),
                    ]),

                Section::make('Description & Medical')
                    ->schema([
                        Textarea::make('description')
                            ->rows(5)
                            ->disabled(fn ($record) => $record?->status === 'adopted')
                            ->columnSpanFull(),
                        Textarea::make('medical_notes')
                            ->rows(5)
                            ->disabled(fn ($record) => $record?->status === 'adopted')
                            ->columnSpanFull(),
                    ]),

                Section::make('Photos')
                    ->schema([
                        Repeater::make('photos')
                            ->relationship()
                            ->disabled(fn ($record) => $record?->status === 'adopted')
                            ->schema([
                                FileUpload::make('file_path')
                                    ->label('Photo')
                                    ->image()
                                    ->disk('public')
                                    ->directory('pets')
                                    ->visibility('public')
                                    ->maxSize(8192)
                                    ->imageResizeMode('cover')
                                    ->imageResizeTargetWidth('800')
                                    ->imageResizeTargetHeight('600')
                                    ->acceptedFileTypes(['image/jpeg', 'image/jpg', 'image/png', 'image/webp', 'image/gif'])
                                    ->saveUploadedFileUsing(function (TemporaryUploadedFile $file): string {
                                        // Generate unique filename
                                        $filename = 'pets/'.uniqid().'.'.$file->getClientOriginalExtension();

                                        // Resize and crop image to 800x600
                                        $resizedImage = self::resizeAndCropImage($file->getRealPath(), 800, 600);

                                        // Store compressed image
                                        Storage::disk('public')->put($filename, $resizedImage);

                                        return $filename;
                                    })
                                    ->required(),
                                Toggle::make('is_primary')
                                    ->label('Primary Photo')
                                    ->helperText('Only one photo should be marked as primary'),
                                TextInput::make('display_order')
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0),
                            ])
                            ->columns(3)
                            ->defaultItems(0)
                            ->collapsible(),
                    ]),
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
            'image/gif' => imagecreatefromgif($sourcePath),
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
            'image/gif' => imagegif($destinationImage),
        };
        $imageData = ob_get_clean();

        // Clean up
        imagedestroy($sourceImage);
        imagedestroy($destinationImage);

        return $imageData;
    }
}
