<?php

namespace App\Filament\Resources\RepositoryResource\Pages;

use App\Filament\Resources\RepositoryResource;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Panel;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\PageRegistration;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Route as RouteFacade;

class EditRepositoryGeneral extends EditRecord
{
    protected static string $resource = RepositoryResource::class;

    protected static ?string $navigationLabel = "General";
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-m-squares-2x2';

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

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make([
                View::make('filament.repository-general-header')
                    ->viewData(['repository' => $this->record]),
                Textarea::make('description')
                    ->hint(fn(string|null $state) => $state ? null : 'This repository has no description.')
                    ->live()
            ])->columnSpan(1),
            Section::make([
                View::make('filament.repository-push-info-short')
                    ->viewData(['repository' => $this->record])
            ])->columnSpan(1),
            Section::make([
                RichEditor::make('overview')
                    ->hint('An overview describes what your image does and how to run it. It displays in the public view of your repository once you have pushed some content.')
            ])
        ]);
    }
}
