<?php

namespace App\Facades;

use App\Services\NamingService;
use Illuminate\Support\Facades\Facade;

class NameValidator extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return NamingService::class;
    }
}
