<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RepositoryResource\Pages;
use App\Models\Repository;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class RepositoryResource extends Resource
{
    protected static ?string $model = Repository::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;
    protected static ?string $pluralModelLabel = 'Repositories';
    protected static ?string $modelLabel = 'Repository';
    protected static ?string $recordRouteKeyName = 'name';
    protected static ?string $recordTitleAttribute = 'path';

    public static function getSlug(): string
    {
        return '';
    }

    public static function getUrl(string $name = 'index', array $parameters = [], bool $isAbsolute = true, ?string $panel = null, ?Model $tenant = null): string
    {
        if(isset($parameters['record']) && !isset($parameters['user']))
            $parameters['user'] = $parameters['record']->owners->first();

        return parent::getUrl($name, $parameters, $isAbsolute, $panel, $tenant);
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
            Pages\EditRepositoryGeneral::class,
            Pages\ManageRepositoryTags::class,
            Pages\ManageRepositoryCollaborators::class,
            Pages\ManageRepositoryWebhooks::class,
            Pages\EditRepositorySettings::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRepositories::route('/repositories'),
            'create' => Pages\CreateRepository::route('/repositories/new'),
            'edit' => Pages\EditRepositoryGeneral::route('/{user:slug}/{record:name}/edit'),
            'edit-settings' => Pages\EditRepositorySettings::route('/{user:slug}/{record:name}/settings'),
            'edit-tags' => Pages\ManageRepositoryTags::route('/{user:slug}/{record:name}/tags'),
            'edit-collaborators' => Pages\ManageRepositoryCollaborators::route('/{user:slug}/{record:name}/collaborators'),
            'edit-webhooks' => Pages\ManageRepositoryWebhooks::route('/{user:slug}/{record:name}/webhooks'),
        ];
    }
}
