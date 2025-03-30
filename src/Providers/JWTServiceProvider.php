<?php

namespace Kyojin\JWT\Providers;

use Illuminate\Support\ServiceProvider;
use Kyojin\JWT\Commands\Setup;
use Kyojin\JWT\Facades\JWT;
use Kyojin\JWT\Http\Middleware\JwtAuthMiddleware;
use Kyojin\JWT\Services\JWTService;

class JWTServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/jwt.php', 'jwt');
        
        $this->app->singleton('jwt', function ($app) {
            return new JWTService();
        });
        
        $this->app->alias('jwt', JWT::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerMiddleware();
        $this->registerPublishing();
        $this->registerCommands();
    }
    
    /**
     * Register middleware.
     *
     * @return void
     */
    protected function registerMiddleware()
    {
        $router = $this->app['router'];
        
        $method = method_exists($router, 'aliasMiddleware') ? 'aliasMiddleware' : 'middleware';
        $router->$method('jwt', JwtAuthMiddleware::class);
    }
    
    /**
     * Register publishable assets.
     *
     * @return void
     */
    protected function registerPublishing()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../../config/jwt.php' => config_path('jwt.php'),
            ], 'jwt-config');
        }
    }
    
    /**
     * Register console commands.
     *
     * @return void
     */
    protected function registerCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Setup::class,
            ]);
        }
    }
}