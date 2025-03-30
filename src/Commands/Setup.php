<?php

namespace Kyojin\JWT\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Random\RandomException;

/**
 * Class Setup
 * 
 * A console command to set up JWT (JSON Web Token) configuration for the application.
 * This command handles the creation or update of environment variables and publishes
 * the JWT configuration file.
 */
class Setup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jwt:setup {--force : Force overwrite of existing configuration file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup JWT configuration file and environment variables';

    /**
     * Execute the console command.
     *
     * Generates a JWT secret, sets up environment variables, and publishes configuration.
     *
     * @return void
     * @throws RandomException
     */
    public function handle(): void
    {
        $envPath = $this->laravel->basePath('.env');
        $envExists = file_exists($envPath);
        $secret = bin2hex(random_bytes(32));
        
        if ($envExists) {
            $this->info('Adding JWT variables to .env file');
            $this->updateEnv($envPath, $secret);
        } else {
            $this->info('Creating .env file');
            $this->createEnv($envPath, $secret);
        }

        $this->publishConfig();

        Artisan::call('config:clear');
        Artisan::call('cache:clear');

        $this->info('JWT environment variables and configuration have been set up successfully.');
    }

    /**
     * Updates an existing .env file with JWT configuration variables.
     *
     * @param string $path The path to the .env file
     * @param string $secret The generated JWT secret key
     * @return void
     */
    private function updateEnv(string $path, string $secret): void
    {
        $content = file_get_contents($path);
        
        if (str_contains($content, 'JWT_SECRET=')) {
            $content = preg_replace(
                '/JWT_SECRET=.*/',
                'JWT_SECRET=' . $secret,
                $content
            );
            $this->info('Existing JWT_SECRET updated.');
        } else {
            $content .= PHP_EOL . 'JWT_SECRET=' . $secret . PHP_EOL;
            $this->info('JWT_SECRET added to .env file.');
        }
        
        if (!str_contains($content, 'JWT_ALGO=')) {
            $content .= 'JWT_ALGO=HS256' . PHP_EOL;
        }
        
        if (!str_contains($content, 'JWT_TTL=')) {
            $content .= 'JWT_TTL=3600' . PHP_EOL;
        }
        
        file_put_contents($path, $content);
        $this->info('.env file updated.');
    }

    /**
     * Creates a new .env file with basic configuration and JWT variables.
     *
     * @param string $path The path where the .env file should be created
     * @param string $secret The generated JWT secret key
     * @return void
     */
    private function createEnv(string $path, string $secret): void
    {
        $envContents = <<<EOT
APP_NAME=Laravel
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

JWT_SECRET={$secret}
JWT_TTL=3600
JWT_ALGO=HS256

EOT;

        file_put_contents($path, $envContents);
        $this->info('.env file created with JWT settings.');
        $this->warn('Please check your .env file for any other required settings.');
    }

    /**
     * Publishes the JWT configuration file to the application's config directory.
     *
     * @return void
     */
    private function publishConfig(): void
    {
        $force = $this->option('force');
        
        $this->call('vendor:publish', [
            '--tag' => 'jwt-config',
            '--force' => $force,
        ]);
        
        $this->info('JWT configuration published successfully.');
    }
}