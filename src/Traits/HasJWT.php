<?php

namespace Kyojin\JWT\Traits;

use Kyojin\JWT\Facades\JWT;

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
     * @throws \LogicException If the implementing class doesn't have an 'id' property
     */
    public function payload(): array
    {
        if (!property_exists($this, 'id')) {
            throw new \LogicException('Class using HasJWT trait must have an "id" property');
        }

        return [
            'sub' => $this->id, // Subject (typically the user ID)
        ];
    }

    /**
     * Creates a new JWT token for the current instance.
     *
     * @return string The encoded JWT token
     * @throws \LogicException If token creation fails due to invalid payload
     */
    public function createToken(): string
    {
        try {
            return JWT::encode($this->payload());
        } catch (\Exception $e) {
            throw new \LogicException('Failed to create JWT token: ' . $e->getMessage());
        }
    }

    /**
     * Retrieves the current valid token or creates a new one if none exists.
     *
     * @return string The current or newly created JWT token
     */
    public function getToken(): string
    {
        if (property_exists($this, 'token') && !empty($this->token) && JWT::validate($this->token)) {
            return $this->token;
        }

        return $this->createToken();
    }
}