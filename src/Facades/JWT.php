<?php

namespace Kyojin\JWT\Facades;

use Illuminate\Support\Facades\Facade;

class JWT extends Facade {
    
    protected static function getFacadeAccessor()
    {
        return 'JWT';
    }

}