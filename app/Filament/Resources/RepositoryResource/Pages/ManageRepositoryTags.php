<?php

namespace App\Filament\Resources\RepositoryResource\Pages;

use App\Filament\Resources\RepositoryResource;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;


class ManageRepositoryTags extends ManageRelatedRecords
{
    protected static string $resource = RepositoryResource::class;
    protected static ?string $navigationIcon = 'heroicon-s-tag';
    protected static ?string $navigationLabel = 'Tags';
    protected static string $relationship = 'tags';

    public function getBreadcrumbs(): array
    {
        return [static::getResource()::getPluralModelLabel(), $this->getTitle(), static::getNavigationLabel()];
    }

    public function getTitle(): string|Htmlable
    {
        return $this->record->path;
    }

    public function table(Table $table): Table
    {
        return $table
            ->header(view('filament.repository-tags-header'))
            ->recordTitleAttribute('name')
            ->emptyStateHeading('No tags found')
            ->emptyStateDescription('Push a tagged image to get started.')
            ->contentGrid(['xs' => 1])
            ->columns([
                Tables\Columns\Layout\Stack::make([
                    TextColumn::make('name')
                        ->url('#')
                        ->color('info')
                        ->size(TextColumn\TextColumnSize::Large)
                        ->weight(FontWeight::Bold),
                    TextColumn::make('name')
                        ->weight(FontWeight::Bold),
                    TextColumn::make('name')
                        ->weight(FontWeight::Bold),
                ])
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ]);
    }
}
