<?php

namespace App\Facades;

use App\Models\Space;
use App\Services\CurrentSpaceService;
use Illuminate\Support\Facades\Facade;

/**
 * @method static Space|null get()
 * @method static void set(Space $space)
 * @method static bool check()
 * @method static int|string|null id()
 *
 * * @see \App\Services\CurrentSpace
 */
class CurrentSpace extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return CurrentSpaceService::class;
    }
}
