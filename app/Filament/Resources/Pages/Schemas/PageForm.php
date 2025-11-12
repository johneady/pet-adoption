<?php

namespace App\Filament\Resources\Pages\Schemas;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class PageForm
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
                            ->afterStateUpdated(fn ($state, callable $set, callable $get) => $get('is_special') ? null : $set('slug', \Illuminate\Support\Str::slug($state)))
                            ->columnSpanFull(),
                        TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->disabled(fn (callable $get) => $get('is_special'))
                            ->helperText('URL-friendly version of the title')
                            ->columnSpanFull(),
                    ]),

                Section::make('Content')
                    ->schema([
                        RichEditor::make('content')
                            ->columnSpanFull()
                            ->toolbarButtons([
                                ['bold', 'italic', 'strike', 'link'],
                                ['h2', 'h3'],
                                ['blockquote', 'codeBlock', 'bulletList', 'orderedList'],
                                ['table'],
                                ['undo', 'redo'],
                            ]),
                    ]),

                Section::make('Menu Assignment')
                    ->columns(2)
                    ->schema([
                        Select::make('menu_id')
                            ->label('Main Menu')
                            ->relationship(
                                name: 'menu',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn ($query) => $query->whereNull('parent_id')->visible()
                            )
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(fn (callable $set) => $set('submenu_id', null))
                            ->helperText('Select a menu to assign this page to'),
                        Select::make('submenu_id')
                            ->label('Submenu')
                            ->relationship(
                                name: 'submenu',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn ($query, callable $get) => $query
                                    ->where('parent_id', $get('menu_id'))
                                    ->visible()
                            )
                            ->searchable()
                            ->preload()
                            ->disabled(fn (callable $get) => ! $get('menu_id'))
                            ->helperText('Optional: Select a submenu'),
                        TextInput::make('display_order')
                            ->numeric()
                            ->default(0)
                            ->required()
                            ->helperText('Lower numbers appear first')
                            ->columnSpanFull(),
                    ]),

                Section::make('Publishing')
                    ->columns(2)
                    ->schema([
                        ToggleButtons::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'published' => 'Published',
                            ])
                            ->icons([
                                'draft' => Heroicon::OutlinedDocumentText,
                                'published' => Heroicon::OutlinedCheckCircle,
                            ])
                            ->colors([
                                'draft' => 'gray',
                                'published' => 'success',
                            ])
                            ->inline()
                            ->required()
                            ->default('draft'),
                        Toggle::make('requires_auth')
                            ->label('Requires Authentication')
                            ->default(false)
                            ->helperText('Only show to logged-in users'),
                    ]),

                Section::make('SEO')
                    ->collapsed()
                    ->schema([
                        TextInput::make('meta_title')
                            ->label('Meta Title')
                            ->maxLength(255)
                            ->helperText('Optional: Override the page title for search engines')
                            ->columnSpanFull(),
                        Textarea::make('meta_description')
                            ->label('Meta Description')
                            ->rows(3)
                            ->maxLength(160)
                            ->helperText('Optional: Description for search engine results')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
