<?php

namespace Kyojin\JWT\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Kyojin\JWT\Exceptions\InvalidTokenException;
use Kyojin\JWT\Exceptions\TokenNotFoundException;
use Kyojin\JWT\Facades\JWT;

/**
 * Class JwtAuthMiddleware
 * 
 * Middleware for authenticating requests using JSON Web Tokens (JWT).
 * Validates JWT tokens from the Authorization header and sets the authenticated user.
 */
class JwtAuthMiddleware
{
    /**
     * Handle an incoming request and perform JWT authentication.
     *
     * @param Request $request The incoming HTTP request
     * @param Closure $next The next middleware in the pipeline
     * @return mixed The response from the next middleware
     * @throws TokenNotFoundException If no token is present in the request
     * @throws InvalidTokenException If the token is invalid or authentication fails
     */
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();

        if (empty($token)) {
            throw new TokenNotFoundException('Authentication token not found in request', 401);
        }

        try {
            $this->authenticate($token);
        } catch (InvalidTokenException $e) {
            throw new InvalidTokenException(
                'Invalid authentication token: ' . $e->getMessage(),
                401,
                $e
            );
        }

        return $next($request);
    }

    /**
     * Validates the token and sets the authenticated user.
     *
     * @param string $token The JWT token to validate
     * @return void
     * @throws InvalidTokenException If token validation fails
     */
    protected function authenticate(string $token): void
    {
        JWT::validate($token);
        $user = JWT::user();

        Auth::setUser($user);
    }
}