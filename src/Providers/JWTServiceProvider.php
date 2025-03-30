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
        
        $this->app->singleton('JWT', function ($app) {
            return new JWTService();
        });
        
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->addMiddlewareAlias('jwt', JwtAuthMiddleware::class);
        $this->registerPublishing();
        $this->registerCommands();
    }
    
    /**
     * Register middleware.
     *
     * @return void
     */
    
    /**
     * Register a short-hand name for a middleware. For compatibility
     * with Laravel < 5.4 check if aliasMiddleware exists since this
     * method has been renamed.
     *
     * @param string $name
     * @param string $class
     *
     * @return void
     */
    protected function addMiddlewareAlias($name, $class)
    {
        $router = $this->app['router'];

        if (method_exists($router, 'aliasMiddleware')) {
            return $router->aliasMiddleware($name, $class);
        }

        return $router->middleware($name, $class);
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