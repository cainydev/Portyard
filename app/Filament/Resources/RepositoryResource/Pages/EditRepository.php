<?php

namespace App\Filament\Resources\RepositoryResource\Pages;

use App\Filament\Resources\RepositoryResource;
use App\Models\Repository;
use Filament\Pages\Concerns\HasUnsavedDataChangesAlert;
use Filament\Pages\Page;
use Filament\Panel;
use Filament\Resources\Pages\PageRegistration;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Route as RouteFacade;
use function abort_unless;

abstract class EditRepository extends Page
{
    use HasUnsavedDataChangesAlert;

    protected static string $resource = RepositoryResource::class;
    public Repository $repository;

    public static function route(string $path): PageRegistration
    {
        return new PageRegistration(
            page: static::class,
            route: fn (Panel $panel): Route => RouteFacade::get($path, static::class)
                ->middleware(static::getRouteMiddleware($panel))
                ->withoutMiddleware(static::getWithoutRouteMiddleware($panel)),
        );
    }

    /**
     * Get the sub navigation from the resource
     */
    public function getSubNavigation(): array
    {
        return static::getResource()::getRecordSubNavigation($this);
    }

    /**
     * Get the required parameters for the sub navigation routes
     */
    public function getSubNavigationParameters(): array
    {
        return $this->repository->only(['namespace', 'name']);
    }

    /**
     * Initialize the repository page.
     */
    public function mount(?string $namespace, ?string $name): void
    {
        abort_unless(!blank($namespace) && !blank($name), 404);

        $this->repository = Repository::where([
                'namespace' => $namespace,
                'name' => $name,
            ])
            ->with($this->getEagerLoads())
            ->firstOrFail();

        abort_unless($this->authorizeAccess(), 403);
    }

    /**
     * The relations that should be eagerly loaded.
     *
     * @return array<string>
     */
    protected function getEagerLoads(): array
    {
        return [];
    }

    /**
     * Authorize the current request
     */
    abstract protected function authorizeAccess(): bool;

    /**
     * The title of the page.
     *
     * @return string|Htmlable
     */
    public function getTitle(): string|Htmlable
    {
        return $this->repository->path;
    }

    /**
     * The breadcrumbs of the page.
     *
     * @return array<string>
     */
    public function getBreadcrumbs(): array
    {
        return ['Repositories', $this->repository->path, static::getNavigationLabel()];
    }
}
