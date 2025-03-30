<?php


namespace Kyojin\JWT\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class TokenNotFoundException extends Exception {
    

    public function render($request): JsonResponse
    {
        return response()->json([
            'error' => 'Token not found',
            'message' => $this->getMessage() ?? 'The token was not found in the request.'
        ], 401);
    }

}