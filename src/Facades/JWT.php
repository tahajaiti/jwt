<?php

namespace Kyojin\JWT\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class JWT
 * 
 * Provides a facade for accessing the JWT (JSON Web Token) service in a Laravel application.
 * This class serves as a static proxy to the underlying JWT service implementation,
 * allowing for convenient access to JWT functionality throughout the application.
 * 
 * @see \Kyojin\JWT\Services\JWTService The underlying service class
 * @method static string encode(array $payload) Encodes a payload into a JWT token
 * @method static array decode(string $token) Decodes a JWT token into its payload
 * @method static mixed user() Gets the user from the current token
 * @method static bool validate(string $token) Validates a JWT token
 */
class JWT extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string The binding name in the service container
     */
    protected static function getFacadeAccessor(): string
    {
        return 'JWT';
    }
}