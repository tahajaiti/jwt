<?php


namespace Kyojin\JWT\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class InvalidTokenException extends Exception {
    

    public function render($request): JsonResponse
    {
        return response()->json([
            'error' => 'Invalid token',
            'message' => 'The token provided is invalid or expired.'
        ], 401);
    }

}