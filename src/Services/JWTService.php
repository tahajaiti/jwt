<?php

namespace Kyojin\JWT\Services;

use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Config;
use Kyojin\JWT\Exceptions\InvalidTokenException;

/**
 * Class JWTService
 * 
 * A service class for handling JSON Web Token (JWT) operations including
 * encoding, decoding, validation, and user information extraction.
 */
class JWTService
{
    /** @var string The secret key used for signing tokens */
    private string $secret;

    /** @var string The algorithm used for signing (default: HS256) */
    private string $algo;

    /** @var int Default time-to-live in seconds (default: 3600) */
    private int $defTtl;

    /** @var string The current token being processed */
    private string $token;

    /**
     * JWTService constructor.
     * Initializes the service with configuration values from Laravel config or environment variables.
     */
    public function __construct()
    {
        $this->secret = Config::get('jwt.secret', env('JWT_SECRET', 'secret'));
        $this->algo = Config::get('jwt.algo', env('JWT_ALGO', 'HS256'));
        $this->defTtl = Config::get('jwt.ttl', env('JWT_TTL', 3600));
    }

    /**
     * Encodes a payload array into a JWT token.
     *
     * @param array $payload The data to be encoded in the token
     * @return string The encoded JWT token
     */
    public function encode(array $payload): string
    {
        $payload['iat'] = time();
        $payload['exp'] = time() + $this->defTtl;

        $header = json_encode(['alg' => $this->algo, 'typ' => 'JWT']);
        
        $headerBase = self::base64Encode($header);
        $payloadBase = self::base64Encode(json_encode($payload));
        
        $sign = self::sign("$headerBase.$payloadBase");

        return "$headerBase.$payloadBase.$sign";
    }

    /**
     * Decodes a JWT token and returns its payload.
     *
     * @param string $token The JWT token to decode
     * @return array The decoded payload
     * @throws InvalidTokenException If the token is invalid or expired
     */
    public function decode(string $token): array
    {
        $this->token = $token;
        
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            throw new InvalidTokenException('Invalid token format');
        }

        [$headerBase, $payloadBase, $sign] = $parts;

        $header = json_decode(self::base64Decode($headerBase), true);
        $payload = json_decode(self::base64Decode($payloadBase), true);

        if (empty($header) || empty($payload)) {
            throw new InvalidTokenException('Invalid token payload');
        }

        if (!self::verify("$headerBase.$payloadBase", $sign)) {
            throw new InvalidTokenException('Invalid token signature');
        }

        if (time() > $payload['exp']) {
            throw new InvalidTokenException('Token expired');
        }


        return $payload;
    }

    /**
     * Extracts the user object (subject) from the current token.
     *
     * @return User The user model
     * @throws InvalidTokenException
     */
    public function user(): User
    {
        
        if (empty($this->token)){
            throw new InvalidTokenException('No token provided');
        }

        $payload = $this->decode($this->token);

        $user = User::where('id', $payload['sub'])->first();

        if (!$user) {
            throw new InvalidTokenException('No user associated with token');
        }

        return $user;
    }

    /**
     * Validates a JWT token.
     *
     * @param string $token The JWT token to validate
     * @return bool True if the token is valid, false otherwise
     */
    public function validate(string $token): bool
    {
        try {
            $this->decode($token);
            return true;
        } catch (InvalidTokenException $e) {
            return false;
        }
    }

    /**
     * Encodes data using URL-safe Base64 encoding.
     *
     * @param string $data The data to encode
     * @return string The encoded string
     */
    private static function base64Encode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Decodes URL-safe Base64 encoded data.
     *
     * @param string $data The encoded data to decode
     * @return string The decoded data
     */
    private static function base64Decode(string $data): string
    {
        return base64_decode(strtr($data, '-_', '+/'));
    }

    /**
     * Creates a signature for the given data using the secret key and algorithm.
     *
     * @param string $data The data to sign
     * @return string The generated signature
     */
    private function sign(string $data): string
    {
        $algo = str_replace('HS', 'sha', $this->algo);
        return self::base64Encode(hash_hmac($algo, $data, $this->secret, true));
    }

    /**
     * Verifies a signature against the data.
     *
     * @param string $data The data to verify
     * @param string $sign The signature to verify against
     * @return bool True if the signature is valid, false otherwise
     */
    private function verify(string $data, string $sign): bool
    {
        $realSign = $this->sign($data);
        return hash_equals($realSign, $sign);
    }
}