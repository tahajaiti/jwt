<?php

namespace Kyojin\JWT\Traits;

use Kyojin\JWT\Facades\JWT;

trait HasJWT {
    
    public function payload(): array {
        return [
            'sub' => $this->id,
        ];
    }
    
    public function createToken(): string {
        return JWT::encode($this->payload());
    }
}