<?php

namespace App\Filament\Resources\RepositoryResource\Pages;

use App\Filament\Resources\RepositoryResource;
use App\Models\Repository;
use Filament\Actions\Action;
use Filament\Forms\Components\ToggleButtons;
use Filament\Panel;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\PageRegistration;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Route as RouteFacade;

class EditRepositorySettings extends EditRecord
{
    protected static string $resource = RepositoryResource::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-m-cog-6-tooth';
    protected static ?string $navigationLabel = 'Settings';

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
            Section::make('Visibility')
                ->description(fn(Repository $record) => "This repository is " . ($record->public ? 'public' : 'private') . '.')
                ->schema([
                    View::make('filament.text')
                        ->viewData(['text' => 'Public repositories are available to anyone. Private repositories are only available to you.']),
                    ToggleButtons::make('public')
                        ->label('Public Repository')
                        ->live()
                        ->boolean()
                        ->grouped()
                        ->afterStateUpdated(function (Repository $record, bool $state) {
                            $record->public = $state;
                            $record->save();
                        })
                ]),
            Section::make('Delete repository')->schema([
                View::make('filament.text')
                    ->viewData(['text' => 'Deleting a repository will destroy all images stored within it! This action is not reversible.']),
                Actions::make([
                    Action::make('delete')
                        ->icon('heroicon-m-x-mark')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(function (Repository $record) {
                            $record->delete();
                            $this->redirect(ListRepositories::getUrl(), navigate: true);
                        })
                ])
            ])
        ]);
    }

    protected function getFormActions(): array
    {
        return [];
    }
}
