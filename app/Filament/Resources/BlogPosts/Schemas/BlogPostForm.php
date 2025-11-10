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
                            ->directory('blog')
                            ->columnSpanFull(),
                    ]),

                Section::make('Content')
                    ->schema([
                        Textarea::make('excerpt')
                            ->rows(3)
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
                            ->options(function (callable $get) {
                                $currentStatus = $get('status');

                                if ($currentStatus === 'draft') {
                                    return [
                                        'draft' => 'Draft',
                                        'published' => 'Published',
                                    ];
                                }

                                if ($currentStatus === 'published') {
                                    return [
                                        'published' => 'Published',
                                        'archived' => 'Archived',
                                    ];
                                }

                                if ($currentStatus === 'archived') {
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
            ]);
    }
}
