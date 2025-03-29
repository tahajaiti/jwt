<?php

namespace Kyojin\JWT\Providers;

use Illuminate\Support\ServiceProvider;
use Kyojin\JWT\Services\JWTService;

class JWTServiceProvider extends ServiceProvider {
    
    public function register(){
        $this->app->singleton('JWT', function ($app) {
            return new JWTService();
        });

        $this->mergeConfigFrom(__DIR__ . '/../../config/jwt.php', 'jwt');
    }

    public function boot(){

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../../config/jwt.php' => $this->app->basePath('config/jwt.php'),
            ], 'jwt-config');
        }

    }

}