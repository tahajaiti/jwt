<?php

return [
    'secret' => env('JWT_SECRET', 'secret'),
    'algo' => env('JWT_ALGO', 'HS256'),
    'ttl' => env('JWT_TTL', 3600),
];