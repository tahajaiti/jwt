<?php

/**
 * JWT Configuration File
 *
 * This file defines the configuration settings for JSON Web Token (JWT) authentication.
 * The settings are loaded from environment variables but have default fallback values.
 *
 * Configuration options:
 * - `secret`: The secret key used for signing and verifying JWTs.
 * - `algo`: The algorithm used to sign the JWT (e.g., HS256, RS256).
 * - `ttl`: The time-to-live (TTL) for the JWT in seconds (default: 3600s or 1 hour).
 *
 * Ensure that you properly set these values in your `.env`.
 * Or run php artisan jwt:setup for easy integration.
 */

return [
    'secret' => env('JWT_SECRET', 'secret'), // JWT signing secret key
    'algo' => env('JWT_ALGO', 'HS256'), // JWT signing algorithm
    'ttl' => env('JWT_TTL', 3600), // Token expiration time in seconds
];
