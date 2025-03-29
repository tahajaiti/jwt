<?php

namespace Kyojin\JWT\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Kyojin\JWT\Facades\JWT;

class JwtAuthMiddleware {
    
    public function handle(Request $request, Closure $next){
        
        $token = $request->bearerToken();

        if (empty($token)){
            throw new Exception('Token not found', 401);
        }

        try {
            
            JWT::validate($token);
            $user = JWT::user();
            Auth::setUser($user);
        } catch(Exception $e) {
            throw new Exception('Token is invalid', 401);
        }

        return $next($request);
    }

}