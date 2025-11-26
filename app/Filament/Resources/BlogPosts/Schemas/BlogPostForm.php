<?php

namespace App\Filament\Resources\BlogPosts\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class BlogPostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic Information')
                    ->columns(2)
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, callable $set) => $set('slug', \Illuminate\Support\Str::slug($state)))
                            ->columnSpanFull(),
                        TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->columnSpanFull(),
                        FileUpload::make('featured_image')
                            ->label('Featured Image')
                            ->image()
                            ->disk('public')
                            ->directory('blog')
                            ->maxSize(8192)
                            ->acceptedFileTypes(['image/jpeg', 'image/jpg', 'image/png', 'image/webp', 'image/gif'])
                            ->imageResizeMode('cover')
                            ->imageResizeTargetWidth('800')
                            ->imageResizeTargetHeight('600')
                            ->saveUploadedFileUsing(function (TemporaryUploadedFile $file, $livewire): string {
                                // Get the old featured image from the record (if editing)
                                $oldFeaturedImage = method_exists($livewire, 'getRecord') ? $livewire->getRecord()?->featured_image : null;

                                // Delete old featured image if it exists
                                if ($oldFeaturedImage && Storage::disk('public')->exists($oldFeaturedImage)) {
                                    Storage::disk('public')->delete($oldFeaturedImage);
                                }

                                // Generate unique filename
                                $filename = 'blog/'.uniqid().'.'.$file->getClientOriginalExtension();

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
                            })
                            ->columnSpanFull(),
                    ]),

                Section::make('Publishing')
                    ->columns(2)
                    ->schema([
                        Select::make('tags')
                            ->label('Tags')
                            ->relationship('tags', 'name')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn ($state, callable $set) => $set('slug', \Illuminate\Support\Str::slug($state))),
                                TextInput::make('slug')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique('tags', 'slug'),
                            ])
                            ->createOptionUsing(function (array $data): int {
                                return \App\Models\Tag::create($data)->getKey();
                            })
                            ->columnSpanFull(),
                        ToggleButtons::make('status')
                            ->options(function ($record) {
                                $originalStatus = $record?->status;

                                if ($originalStatus === 'draft' || $originalStatus === null) {
                                    return [
                                        'draft' => 'Draft',
                                        'published' => 'Published',
                                    ];
                                }

                                if ($originalStatus === 'published') {
                                    return [
                                        'published' => 'Published',
                                        'archived' => 'Archived',
                                    ];
                                }

                                if ($originalStatus === 'archived') {
                                    return [
                                        'archived' => 'Archived',
                                    ];
                                }

                                return [
                                    'draft' => 'Draft',
                                    'published' => 'Published',
                                    'archived' => 'Archived',
                                ];
                            })
                            ->icons([
                                'draft' => Heroicon::OutlinedDocumentText,
                                'published' => Heroicon::OutlinedCheckCircle,
                                'archived' => Heroicon::OutlinedArchiveBox,
                            ])
                            ->colors([
                                'draft' => 'gray',
                                'published' => 'success',
                                'archived' => 'warning',
                            ])
                            ->inline()
                            ->required()
                            ->default('draft')
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                if ($state === 'published' && ! $get('published_at')) {
                                    $set('published_at', now());
                                }
                            })
                            ->columnSpanFull(),
                    ]),

                Section::make('Content')
                    ->columnSpanFull()
                    ->schema([
                        Textarea::make('excerpt')
                            ->rows(1)
                            ->maxLength(500)
                            ->columnSpanFull(),
                        RichEditor::make('content')
                            ->required()
                            ->columnSpanFull()
                            ->toolbarButtons([
                                ['bold', 'italic', 'strike', 'link'],
                                ['h2', 'h3'],
                                ['blockquote', 'codeBlock', 'bulletList', 'orderedList'],
                                ['undo', 'redo'],
                            ]),
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
