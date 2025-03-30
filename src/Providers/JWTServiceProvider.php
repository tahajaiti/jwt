<?php

namespace Kyojin\JWT\Providers;

use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Kyojin\JWT\Commands\Setup;
use Kyojin\JWT\Facades\JWT;
use Kyojin\JWT\Http\Middleware\JwtAuthMiddleware;
use Kyojin\JWT\Services\JWTService;

class JWTServiceProvider extends ServiceProvider {
    
    public function register(){
        // ServiceProvider::addProviderToBootstrapFile(JWTServiceProvider::class);
        $this->app->singleton('JWT', function ($app) {
            return new JWTService();
        });

        $this->mergeConfigFrom(__DIR__ . '/../../config/jwt.php', 'jwt');

        
    }

    public function boot(){
        
        $this->aliasMiddleware();

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../../config/jwt.php' => $this->app->basePath('config/jwt.php'),
            ], 'jwt-config');
        }

        $this->commands([
            Setup::class,
        ]);
        
    }
    
    protected function aliasMiddleware(){
        $router = $this->app['router'];

        $method = method_exists($router, 'aliasMiddleware') ? 'aliasMiddleware' : 'middleware';

        $router->$method('jwt.auth', JwtAuthMiddleware::class);
    }
}