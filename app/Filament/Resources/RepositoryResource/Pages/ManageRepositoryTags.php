<?php

namespace App\Filament\Resources\RepositoryResource\Pages;

use App\Filament\Resources\RepositoryResource;
use Filament\Panel;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Resources\Pages\PageRegistration;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Route as RouteFacade;

class ManageRepositoryTags extends ManageRelatedRecords
{
    protected static string $resource = RepositoryResource::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-s-tag';
    protected static ?string $navigationLabel = 'Tags';
    protected static string $relationship = 'tags';

    public static function route(string $path): PageRegistration
    {
        return new PageRegistration(
            page: static::class,
            route: fn (Panel $panel): Route => RouteFacade::get($path, static::class)
                ->middleware(static::getRouteMiddleware($panel))
                ->withoutMiddleware(static::getWithoutRouteMiddleware($panel))
                ->scopeBindings(),
        );
    }

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
                Stack::make([
                    TextColumn::make('name')
                        ->url('#')
                        ->color('info')
                        ->size(TextSize::Large)
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
