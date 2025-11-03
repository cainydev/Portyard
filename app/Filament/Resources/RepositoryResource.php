<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RepositoryResource\Pages\CreateRepository;
use App\Filament\Resources\RepositoryResource\Pages\EditRepositoryGeneral;
use App\Filament\Resources\RepositoryResource\Pages\EditRepositorySettings;
use App\Filament\Resources\RepositoryResource\Pages\ListRepositories;
use App\Filament\Resources\RepositoryResource\Pages\ManageRepositoryCollaborators;
use App\Filament\Resources\RepositoryResource\Pages\ManageRepositoryTags;
use App\Filament\Resources\RepositoryResource\Pages\ManageRepositoryWebhooks;
use App\Models\Repository;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Panel;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class RepositoryResource extends Resource
{
    protected static ?string $model = Repository::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;
    protected static ?string $pluralModelLabel = 'Repositories';
    protected static ?string $modelLabel = 'Repository';
    protected static ?string $recordRouteKeyName = 'name';
    protected static ?string $recordTitleAttribute = 'path';

    public static function getSlug(?Panel $panel = null): string
    {
        return '';
    }

    public static function getUrl(?string $name = null, array $parameters = [], bool $isAbsolute = true, ?string $panel = null, ?Model $tenant = null, bool $shouldGuessMissingParameters = false): string
    {
        if (isset($parameters['record']) && $parameters['record'] instanceof Repository) {
            $repository = $parameters['record'];

            if (!isset($parameters['user'])) {
                $parameters['user'] = $repository->owners()->first();
            }
        }

        return parent::getUrl($name, $parameters, $isAbsolute, $panel, $tenant, $shouldGuessMissingParameters);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('users', function ($query) {
                $query->where('users.id', auth()->id());
            });
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            EditRepositoryGeneral::class,
            ManageRepositoryTags::class,
            ManageRepositoryCollaborators::class,
            ManageRepositoryWebhooks::class,
            EditRepositorySettings::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRepositories::route('/repositories'),
            'create' => CreateRepository::route('/repositories/new'),

            'edit' => EditRepositoryGeneral::route('/{user:slug}/{record:name}/edit'),
            'edit-settings' => EditRepositorySettings::route('/{user:slug}/{record:name}/settings'),
            'edit-tags' => ManageRepositoryTags::route('/{user:slug}/{record:name}/tags'),
            'edit-collaborators' => ManageRepositoryCollaborators::route('/{user:slug}/{record:name}/collaborators'),
            'edit-webhooks' => ManageRepositoryWebhooks::route('/{user:slug}/{record:name}/webhooks'),
        ];
    }
}
