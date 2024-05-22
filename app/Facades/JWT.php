<?php

namespace App\Facades;

use Closure;
use Illuminate\Support\Facades\Facade;
use Lcobucci\JWT\UnencryptedToken;

/**
 * @method UnencryptedToken createToken(Closure $closure)
 *
 * @see \App\Services\JwtService
 */
class JWT extends Facade
{
    /**
     * Get the facade accessor
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'App\Services\JWTService';
    }
}
