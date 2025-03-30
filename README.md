# Kyojin JWT Package

A Laravel package for implementing JSON Web Token (JWT) authentication with a robust service layer, middleware, traits, and commands.

## Features

- JWT token generation and validation
- Middleware for protected routes
- Trait for easy token creation in models
- Command-line setup tool
- Facade for convenient access
- Comprehensive error handling

## Requirements

- PHP >= 8.2
- Laravel >= 11.x/ 12.x
- Composer

## Installation

Install the package via Composer:

``` bash
composer require tahajaiti/jwt
```

Run the setup command to configure the package:

``` bash
php artisan jwt:setup
```

- This will:
- Create or update your .env file with JWT variables
- Publish the configuration file to config/jwt.php
- Clear configuration and cache

## Configuration

- The package publishes a configuration file at config/jwt.php. Default values are set in the .env file:

``` bash
JWT_SECRET=your-secret
JWT_ALGO=HS256
JWT_TTL=3600
```

- You can customize these in either the .env file or config/jwt.php:

``` php
return [
    'secret' => env('JWT_SECRET', 'fallback-secret'),
    'algo' => env('JWT_ALGO', 'HS256'),
    'ttl' => env('JWT_TTL', 3600),
];
```

## Usage

### Service Provider

The package automatically registers its service provider. If you need to customize it, add to config/app.php:

``` php
'providers' => [
    // ...
    Kyojin\JWT\Providers\JWTServiceProvider::class,
],
```

### Generating Tokens

#### Using the Trait

- Add the HasJWT trait to your User model:

```php
use Kyojin\JWT\Traits\HasJWT;

class User extends Authenticatable
{
    use HasJWT;

    // Optional: Customize payload
    public function payload(): array
    {
        $payload = parent::payload();
        $payload['role'] = $this->role;
        return $payload;
    }
}
```

- Create a token:

```php
$user = User::find(1);
$token = $user->createToken(); // New token
```

#### Using the Facade

```php
use Kyojin\JWT\Facades\JWT;

$payload = ['sub' => 1, 'role' => 'admin'];
$token = JWT::encode($payload);
```

### Middleware

Protect routes with the JWT authentication middleware:

```php
// In routes/api.php
Route::middleware('jwt')->group(function () {
    Route::get('/test', function () {
        return response()->json(['message' => 'Authenticated']);
    });
});
```

Requests must include an Authorization header:

```text
Authorization: Bearer your-jwt-token
```

### Validating Tokens

The default middleware automatically binds the user to the Auth facade

```php
use Kyojin\JWT\Facades\JWT;

$isValid = JWT::validate($token); // Returns boolean
$payload = JWT::decode($token);   // Returns array or throws exception
$user = Auth::user();            // Returns the user based on the validated token 
$user = JWT::user();            // (Optional method for retrieving the user)
```

## Error Handling

The package throws two main exceptions:

- TokenNotFoundException: When no token is provided (401)
- InvalidTokenException: When token is invalid or expired (401)

Handle them in your application exception handler:

```php
use Kyojin\JWT\Exceptions\InvalidTokenException;
use Kyojin\JWT\Exceptions\TokenNotFoundException;

public function render($request, Throwable $exception)
{
    if ($exception instanceof TokenNotFoundException || $exception instanceof InvalidTokenException) {
        return response()->json(['error' => $exception->getMessage()], 401);
    }
}
```

## Contributing

1. Fork the repository
2. Create your feature branch (git checkout -b feature/name)
3. Commit your changes (git commit -m 'Add new feature')
4. Push to the branch (git push origin feature/name)
5. Open a Pull Request

## License

This package is open source do whatever.
