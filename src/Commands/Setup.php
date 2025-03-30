<?php

namespace Kyojin\JWT\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class Setup extends Command
{
    protected $signature = 'jwt:setup {--force}';
    protected $description = 'Setup JWT configuration file, and env variables';

    public function handle(){
        
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

    private function updateEnv($path, $secret){
        $content = file_get_contents($path);
        
        if (strpos($content, 'JWT_SECRET=') !== false) {
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
        
        if (strpos($content, 'JWT_ALGO=') === false) {
            $content .= 'JWT_ALGO=HS256' . PHP_EOL;
        }
        
        if (strpos($content, 'JWT_TTL=') === false) {
            $content .= 'JWT_TTL=3600' . PHP_EOL;
        }
        
        file_put_contents($path, $content);
        $this->info('.env file updated.');
    }

    private function createEnv($path, $secret){
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

    private function publishConfig()
    {
        $force = $this->option('force');
        
        $this->call('vendor:publish', [
            '--tag' => 'jwt-config',
            '--force' => $force,
        ]);
        
        $this->info('JWT configuration published successfully.');
    }
}