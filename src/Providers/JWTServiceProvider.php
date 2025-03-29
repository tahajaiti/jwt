<?php

namespace Kyojin\JWT\Providers;

use Illuminate\Support\ServiceProvider;
use Kyojin\JWT\Services\JWTService;

class JWTServiceProvider extends ServiceProvider {
    
    public function register(){
        $this->app->singleton('JWT', function ($app) {
            return new JWTService();
        });
    }

    public function boot(){
        
    }

}