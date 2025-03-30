<?php

namespace Kyojin\JWT\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Kyojin\JWT\Exceptions\InvalidTokenException;
use Kyojin\JWT\Exceptions\TokenNotFoundException;
use Kyojin\JWT\Facades\JWT;

class JwtAuthMiddleware {
    
    public function handle(Request $request, Closure $next){
        
        $token = $request->bearerToken();

        if (empty($token)){
            throw new TokenNotFoundException('Token not found', 401);
        }

        try {
            
            JWT::validate($token);
            $user = JWT::user();
            Auth::setUser($user);
        } catch(InvalidTokenException $e) {
            throw new InvalidTokenException('Token is invalid', 401);
        }

        return $next($request);
    }

}