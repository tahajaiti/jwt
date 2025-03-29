<?php


namespace Kyojin\JWT\Services;

use Illuminate\Support\Facades\Config;

class JWTService {

    private string $secret;
    private string $algo;
    private int $defTtl;

    public function __construct(){
        $this->secret = Config::get('jwt.secret', env('JWT_SECRET', 'secret'));
        $this->algo = Config::get('jwt.algo', env('JWT_ALGO', 'HS256'),);
        $this->defTtl = Config::get('jwt.ttl', env('JWT_TTL', 3600));
    }

    public function encode(array $payload){
        $payload['iat']= time();
        $payload['exp']= time() + $this->defTtl; 

        $header = json_encode(['alg' => $this->algo, 'typ' => 'JWT']);
        
        $headerBase = self::base64Encode($header);
        $payloadBase = self::base64Encode(json_encode($payload));
        
        $sign = self::sign("$headerBase.$payloadBase");

        return "$headerBase.$payloadBase.$sign";
    }

    public function decode(string $token){
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return null;
        }

        [$headerBase, $payloadBase, $sign] = $parts;

        $header = json_decode(self::base64Decode($headerBase), true);
        $payload = json_decode(self::base64Decode($payloadBase), true);

        if (!$header || !$payload || empty($header) || empty($payload)) {
            return null;
        }

        if (!self::verify("$headerBase.$payloadBase", $sign)){
            return null;
        }

        if (time() > $payload['exp']) {
            return null;
        }

        return $payload;
    }

    public function validate(string $token): bool
    {
        return $this->decode($token) !== null;
    }

    private static function base64Encode(string $data){
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private static function base64Decode(string $data): string {
        return base64_decode(strtr($data, '-_', '+/'));
    }

    private static function sign(string $data): string
    {
        return self::base64Encode(hash_hmac('sha256', $data, self::$secret, true));
    }

    private static function verify(string $data, string $sign): bool
    {
        $realSign = self::sign($data);
        return hash_equals($realSign, $sign);
    }
}