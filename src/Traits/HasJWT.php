<?php

namespace Kyojin\JWT\Traits;

use Kyojin\JWT\Facades\JWT;
use LogicException;

/**
 * Trait HasJWT
 * 
 * Provides JWT (JSON Web Token) functionality to classes that use this trait.
 * Enables easy token creation and payload generation for authentication purposes.
 */
trait HasJWT
{
    /**
     * Generates the payload array for JWT encoding.
     * 
     * This method can be overridden in the implementing class to add custom claims
     * to the JWT payload. By default, it includes the subject's identifier.
     *
     * @return array The payload data to be encoded in the JWT
     */
    public function payload(): array
    {
        return [
        ];
    }


    /**
     * Generates the default payload array for JWT encoding.
     * 
     * This method is used internally to provide a standard payload structure.
     *
     * @return array The default payload data to be encoded in the JWT
     */
    private function defaultPayload(): array {
        return [
            'sub' => $this->id, // Subject (typically the user ID)
        ];
    }

    /**
     * Creates a new JWT token for the current instance.
     *
     * @return string The encoded JWT token
     * @throws LogicException If token creation fails due to invalid payload
     */
    public function createToken(): string
    {
        // Merge default payload with custom payload
        $payload = array_merge($this->defaultPayload(), $this->payload());

        try {
            return JWT::encode($payload);
        } catch (\Exception $e) {
            throw new LogicException('Failed to create JWT token: ' . $e->getMessage());
        }
    }
}