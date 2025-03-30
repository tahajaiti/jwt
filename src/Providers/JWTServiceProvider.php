<?php

namespace Kyojin\JWT\Providers;

use Illuminate\Foundation\Configuration\Middleware;
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
        
        //register the middlewares aliases
        app("Illuminate\Contracts\Http\Kernel")->setMiddlewareAliases(
            $this->getMiddleware()
        );
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

    /**
     * Returns Laravel's default middlewares with package jwt middleware
     *
     * @return array
     */
    private function getMiddleware(){
        return [
            'auth' => 'Illuminate\\Auth\\Middleware\\Authenticate',
            'auth.basic' => 'Illuminate\\Auth\\Middleware\\AuthenticateWithBasicAuth',
            'auth.session' => 'Illuminate\\Session\\Middleware\\AuthenticateSession',
            'cache.headers' => 'Illuminate\\Http\\Middleware\\SetCacheHeaders',
            'can' => 'Illuminate\\Auth\\Middleware\\Authorize',
            'guest' => 'Illuminate\\Auth\\Middleware\\RedirectIfAuthenticated',
            'password.confirm' => 'Illuminate\\Auth\\Middleware\\RequirePassword',
            'precognitive' => 'Illuminate\\Foundation\\Http\\Middleware\\HandlePrecognitiveRequests',
            'signed' => 'Illuminate\\Routing\\Middleware\\ValidateSignature',
            'throttle' => 'Illuminate\\Routing\\Middleware\\ThrottleRequests',
            'verified' => 'Illuminate\\Auth\\Middleware\\EnsureEmailIsVerified',
            'jwt' => 'Kyojin\\JWT\\Http\\Middleware\\JwtAuthMiddleware'
        ];
    }
}